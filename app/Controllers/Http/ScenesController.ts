import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Scene from "App/Models/Scene";
import Play from "App/Models/Play";

export default class ScenesController {
  public async index({}: HttpContextContract) {}

  public async create({}: HttpContextContract) {}

  public async store({}: HttpContextContract) {}

  public async show({ params, view }: HttpContextContract) {
    const sceneInst = await Scene.query()
      .where("id", params.scene_id)
      .preload("lines", (lineQuery) => {
        lineQuery.orderBy("position", "asc").preload("character");
      });
    const scene = sceneInst[0].serialize();

    const playId = params.play_id;
    const play = await (await Play.findOrFail(playId)).serialize();
    return view.render("scene/show", { scene, play });
  }

  public async createNew({ response, auth, params }: HttpContextContract) {
    const play = await Play.findOrFail(params.id);
    const user = await auth.authenticate();
    const newScene=await Scene.create(
      {
        
          name: 'Nouvelle scène',
          position: 2,
          description:"",
          creatorId:user.id,
          playId:play.id
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

  public async edit({}: HttpContextContract) {}

  public async update({}: HttpContextContract) {}

  public async destroy ({response,params}: HttpContextContract) {
    const sceneId=params.id;
    var scene = await Scene.findOrFail(sceneId);
    await scene.delete();
    return response.redirect().back();
  }
}
