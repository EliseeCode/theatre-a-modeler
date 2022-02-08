import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Play from "App/Models/Play";

export default class PlaysController {
  public dataName = "plays";

  public async index({ view }: HttpContextContract) {
    const plays = await (
      await Play.query().preload("scenes").preload("creator")
    ).map((e) => e.serialize());
    return view.render("play/index", { plays });
  }

  public async createNew({ response,auth }: HttpContextContract) {
    const user = await auth.authenticate();
    const newPlay=await Play.create(
      {
        name: 'Nouvelle Pièce',
        description: "description",
        creatorId: user.id
      }
    );
    return response.redirect().back();
  }

  public async create({ view }: HttpContextContract) {
    const creationType = await Play.$computedDefinitions;
    return view.render("defaultViews/create", {
      creationType,
      dataName: this.dataName,
    });
  }

  public async store({}: HttpContextContract) {}

  public async show({ view, params }: HttpContextContract) {
    const data = await Play.findOrFail(params.id);
    return view.render("defaultViews/show", { data, dataName: this.dataName });
  }

  public async edit({}: HttpContextContract) {}

  public async update({}: HttpContextContract) {}

  public async updateName({ request, params }: HttpContextContract) {
    const newPlayName = request.all().newPlayName;
    const play_id = params.playId;
    var play = await Play.findOrFail(play_id);
    play.name = newPlayName;
    console.log(newPlayName);
    await play.save();
    return play;
  }


  public async destroy({ response,params }: HttpContextContract) {
    const playId=params.id;
    var play = await Play.findOrFail(playId);
    await play.delete();
    return response.redirect().back();
  }
}
