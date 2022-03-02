import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Database from "@ioc:Adonis/Lucid/Database";
import Character from "App/Models/Character";
import Image from "App/Models/Image";
import Line from "App/Models/Line";
import ObjectType from "Contracts/enums/ObjectType";
import { URL } from 'url'

export default class CharactersController {
  public dataName = "characters";

  public async store({ auth, params, request, response }: HttpContextContract) {
    const user = await auth.authenticate();
    const lineId = params.lineId;
    const { name, gender, description } = request.body();
    const imageCharacter = request.file('imageCharacter')
    const character = await Character.create(
      {
        name,
        gender,
        description,
      })
    await (await Line.findOrFail(lineId)).related('character').associate(character);
    console.log(imageCharacter)
    if (imageCharacter) {
      console.log("Image à uploader");

      // Won't use a custom name instead Adonis will auto-generate a random name
      /*const fileName = `${user.id}_${lineId}_${await Hash.make(
        new Date().getTime().toString()
      )}.${imageFile?.extname}`; */ // Audio file naming: {owner_id}_{line_id}_{hashed(timestamp)}

      let message: string;
      try {
        await imageCharacter?.moveToDisk(
          "./images/",
          { contentType: request.header("Content-Type") },
          "local"
        );
        message = `The image file has been successfully saved!`;
        console.log(message);
      } catch (err) {
        message = `An error occured during the save of the image file.\nHere's the details: ${err} `;
        console.log(message);
        return response.json({ status: 0, message });
      }

      if (!imageCharacter.fileName) {
        message = `An error occured during the save of the image file.\nHere's the details: imageFile.fileName is not defined.`;
        console.log(message);
        return response.json({ status: 0, message });
      }

      const locationOrigin = new URL(request.completeUrl()).origin;
      // eval(entityType) -> Play is not defined... Why can't we use import aliases in eval? #FIXME

      const newImage = await Image.create({
        name: imageCharacter.fileName,
        publicPath: `${locationOrigin}/uploads/images/${imageCharacter.fileName}`,
        relativePath: `/uploads/images/${imageCharacter.fileName}`,
        creatorId: user.id,
        size: imageCharacter.size,
        type: imageCharacter.extname,
        // mimeType: request.header("Content-Type"), // It's getting as multipart/form-data
        mimeType: `${imageCharacter.fieldName}/${imageCharacter.extname}`,
      });
      console.log(newImage.publicPath);
      character.imageId = newImage.id;
      await character.related('image').associate(newImage);
    }
    return response.redirect().back();
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
