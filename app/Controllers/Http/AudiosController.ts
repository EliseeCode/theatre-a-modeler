import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Audio from "App/Models/Audio";
import Logger from "@ioc:Adonis/Core/Logger";
import Drive from "@ioc:Adonis/Core/Drive";
import { URL } from "url";
import Version from "App/Models/Version";
import Line from "App/Models/Line";
import ObjectType from "Contracts/enums/ObjectType";
import Database from "@ioc:Adonis/Lucid/Database";

export default class AudiosController {
  public dataName = "audios";
  public async index({ view, auth }: HttpContextContract) {
    const user = await auth.authenticate(); // FIXME: pass this via silentAuth
    const audios = await Audio.query()
      .where("creatorId", user.id) // FIXME: not sure if camelcase
      .preload("line"); //.map((e) => e.serialize());
    if (!audios.length)
      return view.render("lines/index", { error: "No data found..." });
    else
      return view.render("audios/index", {
        data: audios,
        columnsDefinitions: Audio.$columnsDefinitions,
        dataName: this.dataName,
      });
  }

  public async create({ }: HttpContextContract) { }

  public async store({
    request,
    response,
    auth,
  }: HttpContextContract) {
    const user = await auth.authenticate();
    const { lineId, versionId } = request.body();
    const audioFile = await request.file('Blob');

    if (!audioFile)
      return response.json({
        status: 0,
        message: "No audio file specified for upload...",
      });

    let message: string;
    try {
      await audioFile?.moveToDisk(
        "./audios/",
        { contentType: request.header("Content-Type") },
        "local"
      );

      message = `The audio file has been successfully saved!`;
      Logger.info(message);
    } catch (err) {

      message = `An error occured during the save of the audio file.\nHere's the details: ${err} `;
      Logger.error(message);
      return response.json({ status: 0, message });
    }
    //creation de la version si necessaire
    var newVersionId = versionId;
    if (versionId == -1) {
      const newVersion = await Version.create({ name: user.username, creatorId: user.id });
      newVersionId = newVersion.id;
    }
    const version = await Version.findOrFail(newVersionId);

    const locationOrigin = new URL(request.completeUrl()).origin;

    const newAudio = await Audio.create({
      name: audioFile.fileName,
      publicPath: `${locationOrigin}/uploads/audios/${audioFile.fileName}`,
      relativePath: `/uploads/audios/${audioFile.fileName}`,
      langId: 1,
      creatorId: user.id,
      lineId: lineId,
      size: audioFile.size,
      type: audioFile.extname,
      versionId: newVersionId,
      mimeType: `${audioFile.fieldName}/${audioFile.extname}`,
    });
    await newAudio.load("line");
    return { audio: newAudio, status: "success", version }
  }

  public async show({ view, params }: HttpContextContract) {
    const audio = (await Audio.findBy("id", params.id))?.serialize();
    if (audio) {
      return view.render("audios/show", {
        data: audio,
        columnNames: Object.keys(audio),
        columnsDefinitions: Audio.$columnsDefinitions,
        dataName: this.dataName,
      });
    } else return view.render("errors/not-found");
  }

  public async createNewVersion({
    auth,
    request,
    response,
  }: HttpContextContract) {
    console.log("creating new version");
    const characterId = request.body().characterId;
    const user = await auth.authenticate();

    const result = await Database.query()
      .from("versions")
      .select("*")
      .join("audios", "audios.version_id", "versions.id")
      .join("lines", "lines.id", "audios.line_id")
      .where("versions.creator_id", user.id)
      .andWhere("versions.type", ObjectType.AUDIO)
      .andWhere("lines.character_id", characterId)
      .countDistinct("versions.id as nbreVersion");
    // .toSQL();

    let nbreVersion = 0;
    if (result.length > 0) {
      nbreVersion = result[0].nbreVersion;
    }
    const newNumVersion = nbreVersion++;
    const versionName = user.username + "-" + newNumVersion;
    //version creation
    const version = await Version.create({
      name: versionName,
      type: ObjectType.AUDIO,
      creatorId: user.id,
    });

    return response.json(version);
  }

  public async edit({ }: HttpContextContract) { }

  public async update({ }: HttpContextContract) { }

  public async destroy({
    request,
    auth
  }: HttpContextContract) {
    const user = await auth.authenticate();
    const audioId = request.body().audioId;
    const audio = await Audio.findOrFail(audioId);
    if (audio.creatorId == user.id) {
      await Drive.delete(audio.name)
        .then(() => {
          let message = `Successfully deleted the file with id of ${audioId} from drive.`;
          Logger.info(message);
        })
        .catch((err) => {
          let message = `Couldn't delete the file with id of ${audioId} from drive. \n Here's the error log: ${err}`;
          Logger.error(message);
          return { status: "error", message };
        });
      await Audio.query()
        .where("id", audioId)
        .delete()
        .then((status) => {
          let message;
          if (!status) {
            message = `No records found with id of ${audioId} in audios table. So, the deletion couldn't happen.`;
            return { status: "error", message };
          }
        })
        .catch((err) => {
          let message = `Couldn't delete the row with id of ${audioId} from audios table. \n Here's the error log: ${err}`;
          Logger.error(message);
          return { status: "error", message };
        });
    }
    return { status: "success" };
  }

  public async getAudioVersions({
    request,
    auth,
    response,
  }: HttpContextContract) {
    await auth.authenticate();
    const { characterId, versionId, sceneId } = request.all();
    //const scene = await Scene.findOrFail(sceneId);
    const audioVersions = new Set();

    const lines = await Line.query()
      .where("character_id", characterId)
      .andWhere("version_id", versionId)
      .andWhere("scene_id", sceneId)
      .preload("audios", (audioQuery) => {
        audioQuery
          .preload("version", (audioVersion) => {
            audioVersion.preload("audios");
          })
          .preload("creator");
      });
    lines.map((line) => {
      line.audios.map((audio) => {
        if (!(audio.version.doublers?.length || audio.version?.doublers))
          audio.version.doublers = [];
        audio.version.doublers.push(audio.creator);
        audio.version.doublers = Array.from(new Set(audio.version.doublers));
        audioVersions.add(audio.version);
      });
    });
    return response.json({ lines, versions: Array.from(audioVersions) });
  }

  public async getAudiosFromAudioVersion({
    request,
    response,
  }: HttpContextContract) {
    const { audioVersionId } = request.all();
    const audios = await Audio.query()
      .where("version_id", audioVersionId)
      .preload("line");
    return response.json(audios);
  }
}
