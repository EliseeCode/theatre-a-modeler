import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Audio from "App/Models/Audio";
import Logger from "@ioc:Adonis/Core/Logger";
import Drive from "@ioc:Adonis/Core/Drive";
import { URL } from "url";
import Version from "App/Models/Version";
import Scene from "App/Models/Scene";
import Line from "App/Models/Line";
import ObjectType from "Contracts/enums/ObjectType";
import Database from "@ioc:Adonis/Lucid/Database";
import Group from "App/Models/Group";

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

  public async create({}: HttpContextContract) {}

  public async store({
    bouncer,
    request,
    response,
    auth,
  }: HttpContextContract) {
    const user = await auth.authenticate();
    const versionId = request.body().versionId;
    const audioVersion = await Version.findOrFail(versionId);
    const groupId = request.body().groupId;
    const group = await Group.find(groupId);
    await bouncer.with("AudioPolicy").authorize("create", audioVersion, group);
    const audioFile = await request.file("audio");

    if (!audioFile)
      return response.json({
        status: 0,
        message: "No audio file specified for upload...",
      });

    const lineId = request.body().lineId;

    // Won't use a custom name instead Adonis will auto-generate a random name
    /*const fileName = `${user.id}_${lineId}_${await Hash.make(
      new Date().getTime().toString()
    )}.${audioFile?.extname}`; */ // Audio file naming: {owner_id}_{line_id}_{hashed(timestamp)}
    let message: string, status: boolean;
    try {
      await audioFile?.moveToDisk(
        "./audios/",
        { contentType: request.header("Content-Type") },
        "local"
      );
      status = true;
      message = `The audio file has been successfully saved!`;
      Logger.info(message);
    } catch (err) {
      status = false;
      message = `An error occured during the save of the audio file.\nHere's the details: ${err} `;
      Logger.error(message);
      return response.json({ status: 0, message });
    }

    if (!audioFile.fileName) {
      message = `An error occured during the save of the audio file.\nHere's the details: audioFile.fileName is not defined.`;
      Logger.error(message);
      return response.json({ status: 0, message });
    }

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
      versionId: versionId,
      // mimeType: request.header("Content-Type"), // It's getting as multipart/form-data
      mimeType: `${audioFile.fieldName}/${audioFile.extname}`,
    });
    return response.json({
      id: newAudio.id,
      version: versionId,
      public_path: newAudio.publicPath,
    });
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

  public async edit({}: HttpContextContract) {}

  public async update({}: HttpContextContract) {}

  public async destroy({
    request,
    response,
    params,
    bouncer,
  }: HttpContextContract) {
    // Need an authorization (permission) check for delete
    const groupId = request.all().groupId;
    const group = await Group.findOrFail(groupId);
    const audioId = params.id;
    const audio = await Audio.findOrFail(audioId);
    await bouncer.with("AudioPolicy").authorize("delete", audio, group);
    await Drive.delete(audio.name)
      .then(() => {
        let message = `Successfully deleted the file with id of ${params.id} from drive.`;
        Logger.info(message);
      })
      .catch((err) => {
        let message = `Couldn't delete the file with id of ${params.id} from drive. \n Here's the error log: ${err}`;
        Logger.error(message);
      });
    await Audio.query()
      .where("id", params.id)
      .delete()
      .then((status) => {
        let message;
        if (status)
          message = `Successfully deleted the row with id of ${params.id} from audios table.`;
        else
          message = `No records found with id of ${params.id} in audios table. So, the deletion couldn't happen.`;
        Logger.info(message);
        response.json({ status, message });
      })
      .catch((err) => {
        let message = `Couldn't delete the row with id of ${params.id} from audios table. \n Here's the error log: ${err}`;
        Logger.error(message);
        response.json({ status: 0, message });
      });
  }

  public async getAudioVersions({
    request,
    auth,
    response,
  }: HttpContextContract) {
    const user = await auth.authenticate();
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
    auth,
    response,
  }: HttpContextContract) {
    const user = await auth.authenticate();
    const { audioVersionId } = request.all();
    const audios = await Audio.query()
      .where("version_id", audioVersionId)
      .preload("line");
    return response.json(audios);
  }
}
