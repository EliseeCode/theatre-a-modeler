import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Scene from "App/Models/Scene";
import Play from "App/Models/Play";
import Line from "App/Models/Line";
import CharacterFetcher from "App/Controllers/helperClass/CharacterFetcher";
import Database from "@ioc:Adonis/Lucid/Database";
import Version from "App/Models/Version";

export default class ScenesController {
  public async show({ view, auth }: HttpContextContract) {
    const user = auth?.user;
    return view.render("scene/show", { user_id: user?.id });
  }
  public async edit({ view }: HttpContextContract) {
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
    const scene = await Scene.create({
      name: name,
      position: play.scenes.length,
      description: "",
      creatorId: user.id,
      playId: play.id,
    });
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
    return { lines, characters }
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
  public async update({ request, params, response }: HttpContextContract) {
    const name = request.all().name;
    const scene_id = params.id;
    var scene = await Scene.findOrFail(scene_id);
    scene.name = name;
    await scene.save();
    return response.redirect().back();
  }

  public async destroy({ response, params }: HttpContextContract) {
    const sceneId = params.id;
    var scene = await Scene.findOrFail(sceneId);
    await scene.delete();
    return response.redirect().back();
  }
}
