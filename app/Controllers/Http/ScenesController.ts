import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Scene from "App/Models/Scene";
import Play from "App/Models/Play";
import Line from "App/Models/Line";
import CharacterFetcher from "App/Controllers/helperClass/CharacterFetcher";

export default class ScenesController {
  public async show({ view }: HttpContextContract) {
    return view.render("scene/show");
  }


  public async select({ params, view }: HttpContextContract) {
    return view.render("scene/", {});
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

  public async edit({ params, view }: HttpContextContract) {
    const scene = await Scene.findOrFail(params.id);

    await scene.load("play", (playQuery) => {
      playQuery.preload("scenes");
    });
    const play = await Play.findOrFail(scene.playId);
    const characterFetcher = new CharacterFetcher();
    //scene.characters = await this.getCharactersFromScene(scene);
    await characterFetcher.getCharactersFromPlay(play);

    //get all the lines from a scene
    await scene.load("lines", (linesQuery) => {
      linesQuery
        .preload("character", (characterQuery) => {
          characterQuery.preload("image");
        })
        .orderBy("lines.position", "asc");
    });

    return view.render("scene/edit", {
      scene,
      play,
    });
  }
  public async lines({ params }: HttpContextContract) {
    const sceneId = params.sceneId;
    const versionId = params.versionId;
    const scene = await Scene.findOrFail(sceneId);
    //get all the lines from a scene and version
    const lines = await Line.query()
      .where('lines.version_id', versionId)
      .andWhere('lines.scene_id', sceneId)
      .orderBy("lines.position", "asc");

    const characterFetcher = new CharacterFetcher();
    const characters = await characterFetcher.getCharactersFromScene(scene);
    return { lines, characters }
  }
  public async getplay({ params }: HttpContextContract) {
    const { sceneId } = params;
    const scene = await Scene.findOrFail(sceneId);
    const play = await Play.findOrFail(scene.playId);
    return play;
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
