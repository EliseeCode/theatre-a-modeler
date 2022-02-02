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

  public async createNew({ auth }: HttpContextContract) {
    const user = await auth.authenticate();
<<<<<<< HEAD
    const newPlay=await Play.create(
      {
        name: 'Nouvelle Pièce',
        description: "description",
        creatorId: user.id
      }
    );
=======
    const newPlay = await Play.create({
      name: "Nouvelle Pièce",
      description: "description",
      creatorId: user.id,
    });
>>>>>>> 981adabbf9984107dd6f793f930f66ea76b13650
    return newPlay;
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

  public async destroy({ params }: HttpContextContract) {
    await Play.query().where("id", params.id).delete();
  }
}
