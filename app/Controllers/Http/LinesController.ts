import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Database from "@ioc:Adonis/Lucid/Database";
import Line from "App/Models/Line";
import User from "App/Models/User";
import Version from "App/Models/Version";
import ObjectType from "Contracts/enums/ObjectType";
import Lines from "Database/migrations/1642771551381_lines";

export default class LinesController {
  public dataName = "lines";

  public async index({ view, auth }: HttpContextContract) {
    const user = await auth.authenticate();
    const lines = (
      await Line.query().where("creatorId", user.id).preload("audios")
    ).map((e) => e.serialize());
    if (!lines.length)
      return view.render("lines/index", { error: "No data found..." });
    return view.render("lines/index", {
      data: lines,
      columnNames: Object.keys(lines[0]),
      columnsDefinitions: Line.$columnsDefinitions,
      dataName: this.dataName,
    });
  }

  public async create({ params, response }: HttpContextContract) {
    const scene_id = params.scene_id;
    const position = parseInt(params.position);
    console.log("creation line at pos:" + position);
    await Line.query().where('sceneId', scene_id).andWhere('position', ">", position).increment("position", 1);
    await Line.create({
      text: "",
      sceneId: scene_id,
      position: position + 1,
      versionId: 1
    });
    return response.redirect().back();
  }








  public async createNewVersion({ auth, params, request, response }: HttpContextContract) {
    const sceneId = request.body().sceneId;
    const characterId = request.body().characterId;
    //const versionName = request.body().name;
    const user = await auth.authenticate();
    const result = await Database.query()
      .from('versions')
      .select('*')
      .join('lines', 'lines.version_id', 'versions.id')
      .where("versions.creator_id", user.id)
      .andWhere("lines.character_id", characterId)
      .countDistinct('lines.version_id as nbreVersion');
    console.log(result);
    let nbreVersion = 0;
    if (result.length > 0) {
      nbreVersion = result[0].nbreVersion;
    }
    const newNumVersion = nbreVersion++;
    const versionName = user.username + "-" + newNumVersion;
    //version creation
    const version = await Version.create({
      name: versionName,
      creatorId: user.id,
      type: ObjectType.CHARACTER
    })
    //collect all line on this scene with this character to grab position
    const lines = await Line.query()
      .where('sceneId', sceneId)
      .andWhere('character_id', characterId)
      .distinct('lines.position');
    //create blueprint for createmany Line
    let newLines: any[] = [];
    for (let line of lines) {
      newLines.push({
        text: "",
        sceneId: sceneId,
        position: line.position,
        versionId: version.id,
        characterId: characterId
      })
    }


    const created_lines = await Line.createMany(newLines);

    return response.json({ lines: created_lines, version: version });
  }












  public async store({ }: HttpContextContract) { }

  public async show({ }: HttpContextContract) { }

  public async edit({ }: HttpContextContract) { }

  public async update({ params, response, request }: HttpContextContract) {
    let line = await Line.findOrFail(params.id);
    const { text, characterId } = request.body();
    line.text = text || "";
    line.characterId = characterId;
    await line.save();
    return response.redirect().back();
  }

  public async updateCharacter({ response, request }: HttpContextContract) {

    const { characterId, lineId } = request.body();
    let line = await Line.findOrFail(lineId);
    line.characterId = characterId;
    console.log(line);
    await line.save();
    return "ok";
  }
  public async updateText({ response, request }: HttpContextContract) {

    const { text, lineId } = request.body();
    let line = await Line.findOrFail(lineId);
    line.text = text || "";
    await line.save();
    return "ok";
  }

  public async splitAText({ auth, params, response, request }: HttpContextContract) {

    const user = await auth.authenticate();
    const { firstPart, secondPart, lineId, sceneId } = request.body();
    let line = await Line.findOrFail(lineId);
    line.text = firstPart || "";
    await line.save();
    let position = line.position;
    await Line.query().where('sceneId', sceneId).andWhere('position', ">", position).increment("position", 1);
    await Line.create({
      text: secondPart || "",
      sceneId: sceneId,
      position: position + 1,
      versionId: 1,
      creatorId: user.id
    });
    return response.redirect().back();
  }

  public async destroy({ params, response }: HttpContextContract) {
    let line = await Line.findOrFail(params.id);
    await Line.query().where('sceneId', line.sceneId).andWhere('position', ">", line.position).decrement("position", 1);
    await line.delete();
    return response.redirect().back();
  }
}
