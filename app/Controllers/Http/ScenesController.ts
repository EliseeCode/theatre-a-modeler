import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Scene from "App/Models/Scene";
import Play from "App/Models/Play";
import Logger from "@ioc:Adonis/Core/Logger";
export default class ScenesController {
  public async index({ }: HttpContextContract) { }

  public async create({ }: HttpContextContract) { }

  public async store({ }: HttpContextContract) { }

  public async show({ params, view }: HttpContextContract) {
    const sceneInst = await Scene.query()
      .where("id", params.scene_id)
      .preload("lines", (lineQuery) => {
        lineQuery.orderBy("position", "asc").preload("character");
      });
    const scene = sceneInst[0].serialize();

    var userByCharacter = {
      1: 1,
      2: 2
    };


    const sceneInst2 = await Scene.query().preload("lines", (lineQuery) => {
      for (let characterId in userByCharacter) {
        lineQuery.where('characterId', characterId).preload("audios", (audioQuery) => {
          audioQuery.where("creator_id", userByCharacter[characterId])
        })
      }
    })



    const playId = params.play_id;
    const play = await (await Play.findOrFail(playId)).serialize();
    return view.render("scene/show", { scene, play, sceneInst2 });
  }

  public async createNew({ response, auth, params }: HttpContextContract) {
    const play = await Play.findOrFail(params.id);
    const user = await auth.authenticate();
    const newScene = await Scene.create(
      {

        name: 'Nouvelle scène',
        position: 2,
        description: "",
        creatorId: user.id,
        playId: play.id
      }
    );
    return response.redirect().back();
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

  public async edit({ }: HttpContextContract) { }

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
