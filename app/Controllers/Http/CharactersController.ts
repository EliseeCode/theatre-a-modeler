import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Database from "@ioc:Adonis/Lucid/Database";
import Audio from "App/Models/Audio";
import Character from "App/Models/Character";
import Line from "App/Models/Line";
import Scene from "App/Models/Scene";
import Version from "App/Models/Version";
import ObjectType from "Contracts/enums/ObjectType";
import ImageUploader from '../helperClass/ImageUploader';
import Drive from "@ioc:Adonis/Core/Drive";

export default class CharactersController {
  public dataName = "characters";

  public async store({ auth, request }: HttpContextContract) {
    console.log('store Character');
    const user = await auth.authenticate();
    const lineId = request.body().lineId;
    const { name, gender, description, action } = request.body();
    var characterId = request.body().characterId || -1;
    const imageCharacter = request.file('imageCharacter');

    if (action == "new") {
      characterId = -1;
    }
    console.log(characterId);

    // const character = await Character.updateOrCreate({ id: characterId },
    //   {
    //     name,
    //     gender,
    //     description,
    //   });
    var character = await Character.find(characterId) || null;
    if (character) {
      character.name = name;
      character.gender = gender;
      character.description = description;
      await character.save();
    }
    else {
      character = await Character.create({
        name, gender, description
      });
    };
    const line = await Line.findOrFail(lineId)
    //if new character=>associate_it
    if (characterId < 0) {
      await line.related('character').associate(character);
      await (await (Scene.findOrFail(line.sceneId))).related('characters').attach([character.id]);
    }
    if (imageCharacter) {
      const newImage = await (new ImageUploader()).uploadImage(imageCharacter, request, user);
      if (newImage) {
        character.imageId = newImage.id;
        //await character.save();
        await character.related('image').associate(newImage);
        await character.load('image');
      }
    }
    console.log('characterImage', JSON.stringify(character.image))
    return { character, status: "success" };
    //return { character };
  }

  public async detach({ request }: HttpContextContract) {
    const { characterId, sceneId } = request.body();
    console.log(characterId);
    console.log(sceneId);
    await (await Scene.findOrFail(sceneId)).related("characters").detach([characterId]);
    return { status: "success" };
  }

  public async createTextVersion({ request, auth }: HttpContextContract) {
    const sceneId = request.body().sceneId;
    const characterId = request.body().characterId;

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

    return { lines: created_lines, version: version, characterId };
  }

  public async removeTextVersion({ request, bouncer }: HttpContextContract) {
    const { textVersionId } = request.body();
    if (textVersionId == 1) { return; }
    var version = await Version.findOrFail(textVersionId);
    await bouncer.with("VersionPolicy").authorize("delete", version);
    await version.delete();
    return { status: 'success' }
  }
  public async removeAudioVersion({ request, bouncer }: HttpContextContract) {
    const { audioVersionId } = request.body();
    var version = await Version.findOrFail(audioVersionId);
    await bouncer.with("VersionPolicy").authorize("delete", version);
    const audios = await Audio.query().where("version_id", audioVersionId);
    for (let k in audios) {
      let audio = audios[k];
      await Drive.delete(audio.name)
      await audio.delete();
    }
    await version.delete();
    return { status: 'success' }
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
