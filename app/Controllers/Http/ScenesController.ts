import { HttpContextContract } from '@ioc:Adonis/Core/HttpContext'
import Scene from 'App/Models/Scene';
import Play from 'App/Models/Play';

export default class ScenesController {
  public async index ({}: HttpContextContract) {
  }

  public async create ({}: HttpContextContract) {
  }

  public async store ({}: HttpContextContract) {
  }

  public async show ({params,view}: HttpContextContract) {
    const sceneInst = await Scene.query().where('id',params.scene_id).preload("lines",(lineQuery)=>{lineQuery.orderBy('position', 'asc').preload('character')});
    const scene=sceneInst[0].serialize();
    const play_id=params.play_id;
    const play=await (await Play.findOrFail(play_id)).serialize()
    return view.render("scene/show",{scene,play});
  }

  public async edit ({}: HttpContextContract) {
  }

  public async update ({}: HttpContextContract) {
  }

  public async destroy ({}: HttpContextContract) {
  }
}
