import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Database from "@ioc:Adonis/Lucid/Database";
import Character from "App/Models/Character";
import Play from "App/Models/Play";
import ObjectType from "Contracts/enums/ObjectType";
import Lines from "Database/migrations/1642771551381_lines";

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

  public async store({ }: HttpContextContract) { }

  public async show({ view, params }: HttpContextContract) {
    const characterId = params.id;
    const character = await Character.findOrFail(params.id);
    await character.load("image");
    const objectType = ObjectType

    const plays = await Play.query().preload('scenes', (sceneQuery) => {
      sceneQuery.preload("lines", (lineQuery) => {
        lineQuery.where("character_id", characterId)
      })
    }).distinct("plays.id").select("name")


    return view.render("characters/show", { character, plays, objectType });
  }

  public async edit({ }: HttpContextContract) { }

  public async update({ }: HttpContextContract) { }

  public async destroy({ params }: HttpContextContract) {
    await Character.query().where("id", params.id).delete();
  }
}
