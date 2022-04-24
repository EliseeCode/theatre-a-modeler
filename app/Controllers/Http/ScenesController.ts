import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Scene from "App/Models/Scene";
import Play from "App/Models/Play";
import Line from "App/Models/Line";
import CharacterFetcher from "App/Controllers/helperClass/CharacterFetcher";
import Database from "@ioc:Adonis/Lucid/Database";
import Version from "App/Models/Version";
import ImageUploader from "../helperClass/ImageUploader";

export default class ScenesController {
  public async show({ view, auth, params }: HttpContextContract) {
    await Scene.findOrFail(params.id);
    const user = auth?.user;
    return view.render("scene/show", { user_id: user?.id });
  }
  public async edit({ view, bouncer, params }: HttpContextContract) {
    const scene = await Scene.findOrFail(params.id);
    await bouncer.with("ScenePolicy").authorize("update", scene);
    return view.render("scene/edit");
  }

  public async index({ }: HttpContextContract) { }

  public async create({ }: HttpContextContract) { }

  public async store({ }: HttpContextContract) { }

  public async createNew({
    bouncer,
    response,
    auth,
    params,
    request,
  }: HttpContextContract) {
    const playId = params.id;
    const play = await Play.findOrFail(playId);
    await bouncer.with("PlayPolicy").authorize("update", play);
    await play.load("scenes");
    const user = await auth.authenticate();
    const name = request.body().name || "Scène sans nom";
    const { imageFile, imageId } = request.body();

    const scene = await Scene.create({
      name: name,
      position: play.scenes.length,
      description: "",
      creatorId: user.id,
      playId: play.id,
    });

    if (imageId) {
      scene.imageId = imageId;
      await scene.save();
    }
    if (imageFile) {
      const newImage = await (new ImageUploader()).uploadImage(imageFile, request, user);
      if (newImage) {
        scene.imageId = newImage.id;
        await scene.save();
        // await scene.related('image').associate(newImage);
        // await scene.load('image');
      }
    }

    return response.redirect("/scene/" + scene.id + "/edit");
  }

  public async updateName({ request, params }: HttpContextContract) {
    const newSceneName = request.all().newSceneName;
    const scene_id = params.sceneId;
    var scene = await Scene.findOrFail(scene_id);
    scene.name = newSceneName;
    console.log(newSceneName);
    await scene.save();
    return scene;
  }




  public async lines({ params }: HttpContextContract) {
    const sceneId = params.sceneId;
    const versionId = params.versionId;
    const scene = await Scene.findOrFail(sceneId);
    //get all the lines from a scene and version
    if (versionId) {
      var lines = await Line.query()
        .where('lines.version_id', versionId)
        .andWhere('lines.scene_id', sceneId)
        .orderBy("lines.position", "asc");
    }
    else {
      var lines = await Line.query()
        .where('lines.scene_id', sceneId)
        .orderBy("lines.position", "asc");
    }
    const characterFetcher = new CharacterFetcher();
    const characters = await characterFetcher.getCharactersFromScene(scene);
    const textVersions = await Database.query().select('versions.*').select('lines.character_id').from('versions').join("lines", "versions.id", "lines.version_id").where("lines.scene_id", sceneId);
    return { lines, characters, textVersions }
  }

  public async getPlay({ params }: HttpContextContract) {
    const { sceneId } = params;
    const scene = await Scene.findOrFail(sceneId);
    const play = await Play.findOrFail(scene.playId);
    return play;
  }

  public async getAudios({ response, params }: HttpContextContract) {
    const { sceneId } = params;
    const audios = await Database.from("audios")
      .select("audios.*")
      .select("versions.name as versionName")
      .join('versions', 'audios.version_id', '=', 'versions.id')
      .join('lines', 'audios.line_id', '=', 'lines.id')
      .where('lines.scene_id', sceneId);

    const versions = await Version.query().whereIn('id', audios.map((audio) => { return audio.version_id }));

    console.log(audios.map((aud) => { return aud.id }));
    return response.json({ audios, versions });

  }

  public async update({ request, auth }: HttpContextContract) {


    console.log('store or update scene');
    const user = await auth.authenticate();
    const { sceneId, playId, name, imageId, description } = request.body();
    const imageScene = request.file('imageScene');

    const scene = await Scene.updateOrCreate({ id: sceneId },
      {
        name,
        description,
        imageId,
        playId
      });
    //check if create Or Update
    await scene.save();

    //if new scene=>associate_it
    if (imageScene && !imageId) {
      const newImage = await (new ImageUploader()).uploadImage(imageScene, request, user);
      if (newImage) {
        scene.imageId = newImage.id;
        await scene.save();
        //await scene.related('image').associate(newImage);
        await scene.load('image');
      }
    }
    console.log('sceneImage', JSON.stringify(scene.image))
    return { scene, status: "success" };
    //return { character };
  }

  public async destroy({ response, params, bouncer }: HttpContextContract) {
    const sceneId = params.id;
    var scene = await Scene.findOrFail(sceneId);
    await bouncer.with("ScenePolicy").authorize("delete", scene);
    await scene.delete();
    return response.redirect().back();
  }
}
