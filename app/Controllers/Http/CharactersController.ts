import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Database from "@ioc:Adonis/Lucid/Database";
import Character from "App/Models/Character";
import Line from "App/Models/Line";
import Play from "App/Models/Play";
import ObjectType from "Contracts/enums/ObjectType";


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

  public async store({ request, response }: HttpContextContract) {
    const lineId = request.body().lineId;
    const character = await Character.create(
      {
        name: "Personnage sans nom",
        gender: "Other",
      })
    await (await Line.findOrFail(lineId)).related('character').associate(character);
    return response.redirect('/characters/' + character.id)
  }

  public async show({ view, params }: HttpContextContract) {
    const characterId = params.id;
    const character = await Character.findOrFail(params.id);
    await character.load("image");
    const objectType = ObjectType

    Database.query();
    const data = await Database.from('plays')
      .join('scenes', 'plays.id', '=', 'scenes.play_id')
      .join('lines', 'scenes.id', '=', 'lines.scene_id')
      .select("plays.name as playName", "scenes.name as sceneName", "scenes.id as sceneId")
      .where('lines.character_id', characterId);

    const playData = data.reduce(function (acc, cur) {
      (acc[cur["playName"]] = acc[cur["playName"]] || { scenes: [], name: cur["playName"] }).scenes.push(cur);
      return acc;
    }, {});

    console.log(playData);
    // data.reduce((prevPlay,currPlay,index)=>{
    //   Play.
    // })


    return view.render("characters/show", { character, playData, objectType });
  }

  public async edit({ }: HttpContextContract) { }

  public async update({ response, params, request }: HttpContextContract) {

    const characterId = params.id;
    const { name, description, gender } = request.all();
    const character = await Character.findOrFail(characterId);
    console.log(characterId, name, description, gender);
    character.name = name || "Personnage sans nom";
    character.description = description || "";
    character.gender = gender || "Other";
    await character.save();

    return response.redirect().back();
  }

  public async destroy({ params }: HttpContextContract) {
    await Character.query().where("id", params.id).delete();
  }
}
