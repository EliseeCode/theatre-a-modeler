import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Image from "App/Models/Image";
import Play from "App/Models/Play";
import Scene from "App/Models/Scene";
import User from "App/Models/User";
import Character from "App/Models/Character";
import Logger from "@ioc:Adonis/Core/Logger";
import Drive from "@ioc:Adonis/Core/Drive";
import { URL } from "url";
import ObjectType from "Contracts/enums/ObjectType";

export default class ImagesController {
  public async index({ }: HttpContextContract) { }

  public async create({ }: HttpContextContract) { }

  public async store({ request, response, auth }: HttpContextContract) {
    const imageFile = await request.file("image");
    if (!imageFile)
      return response.json({
        status: 0,
        message: "No image file specified for upload...",
      });
    console.log("1");
    const user = await auth.authenticate();
    const entityType = parseInt(request.body().entityType);
    const entityId = request.body().entityId;
    console.log(entityType, [
      ObjectType.PLAY,
      ObjectType.SCENE,
      ObjectType.LINE,
      ObjectType.CHARACTER,
      ObjectType.USER,
    ]);
    if (
      ![
        ObjectType.PLAY,
        ObjectType.SCENE,
        ObjectType.LINE,
        ObjectType.CHARACTER,
        ObjectType.USER,
      ].includes(entityType)
    ) {
      let message = `Invalid entity type :(${entityType}) for upload...`;
      Logger.info(message);
      return response.json({
        status: 0,
        message: `Invalid entity type (${entityType}) for upload...`,
      });
    }

    // Won't use a custom name instead Adonis will auto-generate a random name
    /*const fileName = `${user.id}_${lineId}_${await Hash.make(
      new Date().getTime().toString()
    )}.${imageFile?.extname}`; */ // Audio file naming: {owner_id}_{line_id}_{hashed(timestamp)}
    let message: string;
    try {
      await imageFile?.moveToDisk(
        "./images/",
        { contentType: request.header("Content-Type") },
        "local"
      );
      message = `The image file has been successfully saved!`;
      Logger.info(message);
    } catch (err) {
      message = `An error occured during the save of the image file.\nHere's the details: ${err} `;
      Logger.error(message);
      return response.json({ status: 0, message });
    }

    if (!imageFile.fileName) {
      message = `An error occured during the save of the image file.\nHere's the details: imageFile.fileName is not defined.`;
      Logger.error(message);
      return response.json({ status: 0, message });
    }

    const locationOrigin = new URL(request.completeUrl()).origin;
    // eval(entityType) -> Play is not defined... Why can't we use import aliases in eval? #FIXME
    let entityModel;
    switch (entityType) {
      case ObjectType.PLAY:
        entityModel = Play;
        break;
      case ObjectType.SCENE:
        entityModel = Scene;
        break;
      case ObjectType.USER:
        entityModel = User;
        break;
      case ObjectType.CHARACTER:
        entityModel = Character;
        break;
    } // a stupid switch...
    const entity = await entityModel.findOrFail(entityId);

    const newImage = await Image.create({
      name: imageFile.fileName,
      publicPath: `${locationOrigin}/uploads/images/${imageFile.fileName}`,
      relativePath: `/uploads/images/${imageFile.fileName}`,
      creatorId: user.id,
      size: imageFile.size,
      type: imageFile.extname,
      // mimeType: request.header("Content-Type"), // It's getting as multipart/form-data
      mimeType: `${imageFile.fieldName}/${imageFile.extname}`,
    });

    entity.imageId = newImage.id;
    entity.image = newImage;
    await entity.save();

    return newImage;
  }

  public async show({ }: HttpContextContract) { }

  public async edit({ }: HttpContextContract) { }

  public async update({ }: HttpContextContract) { }

  public async destroy({ response, params }: HttpContextContract) {
    const image = (await Image.query().where("id", params.id))[0];
    if (!(await Drive.exists(image.name))) {
      let message = `Couldn't find the image with id of ${params.id} in the drive.`;
      Logger.error(message);
    } else {
      await Drive.delete(image.name)
        .then(() => {
          let message = `Successfully deleted the image with id of ${params.id} from drive.`;
          Logger.info(message);
        })
        .catch((err) => {
          let message = `Couldn't delete the image with id of ${params.id} from drive. \nHere's the error log: ${err}`;
          Logger.error(message);
        });
    }
    await Image.query()
      .where("id", params.id)
      .delete()
      .then((status) => {
        let message;
        if (status)
          message = `Successfully deleted the row with id of ${params.id} from images table.`;
        else
          message = `No records found with id of ${params.id} in images table. So, the deletion couldn't happen.`;
        Logger.info(message);
        response.json({ status, message });
      })
      .catch((err) => {
        let message = `Couldn't delete the row with id of ${params.id} from images table. \nHere's the error log: ${err}`;
        Logger.error(message);
        response.json({ status: 0, message });
      });
  }
}
