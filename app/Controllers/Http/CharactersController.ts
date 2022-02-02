import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Character from "App/Models/Character";

export default class CharactersController {
  public dataName = "characters";

  public async index({ view }: HttpContextContract) {
    const characters = await Character.all();
    const columnsDefinitions = Character.$columnsDefinitions;
    return view.render("defaultViews/index", {
      columnsDefinitions: Character.$columnsDefinitions,
      data: characters,
      dataName: this.dataName,
    });
  }

  public async create({ view }: HttpContextContract) {
    const creationType = await Character.$computedDefinitions;
    return view.render("defaultViews/create", {
      creationType,
      dataName: this.dataName,
    });
  }

  public async store({}: HttpContextContract) {}

  public async show({ view, params }: HttpContextContract) {
    const data = await Character.findOrFail(params.id);
    return view.render("defaultViews/show", { data, dataName: this.dataName });
  }

  public async edit({}: HttpContextContract) {}

  public async update({}: HttpContextContract) {}

  public async destroy({ params }: HttpContextContract) {
    await Character.query().where("id", params.id).delete();
  }
}
