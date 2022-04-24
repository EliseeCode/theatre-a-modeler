import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Line from "App/Models/Line";


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

  public async create({ auth, request }: HttpContextContract) {
    const user = await auth.authenticate();
    const afterLinePos = parseInt(request.body().afterLinePos);
    const sceneId = parseInt(request.body().sceneId);

    await Line.query().where('sceneId', sceneId)
      .andWhere('version_id', 1)
      .andWhere('position', ">", afterLinePos)
      .increment("position", 1);

    await Line.create({
      text: "",
      sceneId: sceneId,
      position: afterLinePos + 1,
      versionId: 1,
      creatorId: user.id
    });

    const lines = await Line.query()
      .where('lines.version_id', 1)
      .andWhere('lines.scene_id', sceneId)
      .orderBy("lines.position", "asc");

    return lines;
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

  public async updateCharacter({ request }: HttpContextContract) {

    const { characterId, lineId } = request.body();
    let line = await Line.findOrFail(lineId);
    line.characterId = characterId;
    console.log(line);
    await line.save();
    return "ok";
  }
  public async updateText({ request }: HttpContextContract) {

    const { lineId, text } = request.body();
    let lineObj = await Line.findOrFail(lineId);
    lineObj.text = text || "";
    await lineObj.save();
    return "ok";
  }

  public async splitAText({ auth, request }: HttpContextContract) {

    const user = await auth.authenticate();
    const { firstPart, secondPart, lineId } = request.body();
    let line = await Line.findOrFail(lineId);
    line.text = firstPart || "";
    await line.save();
    let position = line.position;
    let sceneId = line.sceneId;

    await Line.query().where('sceneId', sceneId).andWhere('position', ">", position).increment("position", 1);
    const newLine = await Line.create({
      text: secondPart || "",
      sceneId: sceneId,
      position: position + 1,
      versionId: 1,
      creatorId: user.id
    });

    return { newLine, status: "success" };
  }

  public async destroy({ params }: HttpContextContract) {
    let line = await Line.findOrFail(params.lineId);
    const sceneId = line.sceneId;
    await Line.query().where('sceneId', line.sceneId).andWhere('position', ">", line.position).decrement("position", 1);
    await line.delete();

    const lines = await Line.query().where('scene_id', sceneId)
      .andWhere('version_id', 1);
    return lines;
  }
}
