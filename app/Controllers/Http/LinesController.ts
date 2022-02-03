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

  public async create({}: HttpContextContract) {}

  public async store({}: HttpContextContract) {}

  public async show({}: HttpContextContract) {}

  public async edit({}: HttpContextContract) {}

  public async update({}: HttpContextContract) {}

  public async destroy({}: HttpContextContract) {}
}
