import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Scene from "App/Models/Scene";
import Play from "App/Models/Play";
import Logger from "@ioc:Adonis/Core/Logger";
import Group from "App/Models/Group";
import Image from "App/Models/Image";
import Character from "App/Models/Character";
import Status from "Contracts/enums/Status";
import Line from "App/Models/Line";
import CharacterFetcher from "App/Controllers/helperClass/CharacterFetcher";

export default class ScenesController {
  private async getCharacters(model: Play | Scene) {
    const totalCharacters = new Set();
    if (model instanceof Play) {
      await model.load("scenes", (sceneQuery) => {
        sceneQuery.preload("lines", (lineQuery) => {
          lineQuery.preload("character");
        });
      });
      model.scenes.forEach((scene) => {
        scene.lines.forEach((line) => {
          totalCharacters.add(line.characterId);
        });
      });
    } else if (model instanceof Scene) {
      await model.load("lines", (lineQuery) => {
        lineQuery.preload("character", (characterQuery) => {
          characterQuery.preload("image");
        });
      });
      model.lines.forEach((line) => {
        totalCharacters.add(line.characterId);
      });
    } else return null;
    return totalCharacters;
  }
  public async select({ params, view, auth }: HttpContextContract) {
    const user = await auth.authenticate();

    const scene = (
      await Scene.query()
        .where("id", params.scene_id)
        .preload("play")
        .preload("lines", (lineQuery) => {
          lineQuery.preload("audios");
        })
    )[0];

    const totalCharacters = await this.getCharacters(scene); // total characters in this scene
    const payload = {};

    console.log(scene.lines);

    const lines = scene.lines;
    await lines.map(async (line) => await line.load("audios"));

    lines.forEach((line) => {
      console.log(`Going through line with id of: ${line.id}`);
      const lineCharacter = line?.character; // fallback for "didascalie"
      const lineVersion = line?.version?.id ?? 1; // fallback for official version
      if (!payload[lineCharacter?.id ?? 1]) {
        console.log(
          `No record found for character with id of: ${lineCharacter?.id ?? 1
          } on the payload...`
        );
        payload[lineCharacter?.id ?? 1] = { character: null, lines: {} }; // init a new object
      }
      console.log(lineCharacter?.id ?? 1);
      payload[lineCharacter?.id ?? 1].character = lineCharacter.serialize();

      if (!payload[lineCharacter?.id ?? 1].lines[lineVersion]) {
        console.log(
          `No record found for line with version id of: ${lineVersion} on the payload...`
        );
        payload[lineCharacter?.id ?? 1].lines[lineVersion] = {
          line: null,
          doublers: {},
        }; // init a new object
      }

      payload[lineCharacter?.id ?? 1].lines[lineVersion].line =
        line.serialize();

      if (!line.audios) {
        console.log(
          `No attached audios found for line id: ${line.id}: ${line.audios[0]}`
        );
        return;
      }

      line?.audios.forEach((audio) => {
        // get lines' audios
        const audioCreator = audio?.creator; // FIXME not sure for this...
        if (
          !payload[lineCharacter?.id ?? 1].lines[lineVersion].doublers[
          audioCreator.id
          ]
        ) {
          console.log(
            `No record found for audio with creator id of: ${audioCreator} on the payload...`
          );
          payload[lineCharacter?.id ?? 1].lines[lineVersion].doublers[
            audioCreator.id
          ] = { creator: null, audios: new Set() };
        }
        payload[lineCharacter?.id ?? 1].lines[lineVersion].doublers[
          audioCreator.id
        ].creator = audioCreator.serialize();

        payload[lineCharacter?.id ?? 1].lines[lineVersion].doublers[
          audioCreator.id
        ].audios.add(audio.versionId);
      });
    });
    return view.render("scene/select", { payload, user });
  }
  public async index({ }: HttpContextContract) { }

  public async create({ }: HttpContextContract) { }

  public async store({ }: HttpContextContract) { }

  public async show({ request, params, view, response }: HttpContextContract) {
    const data = JSON.parse(request.input("data"));
    const sceneId = params.id;
    const currentGroup = await Group.findOrFail(params.group_id);
    const currentPlay = (
      await currentGroup
        .related("plays")
        .query()
        .where("play_id", params.play_id)
    )[0];
    // TODO add error handlers for relation and query results
    const scenes = await currentPlay
      .related("scenes")
      .query()
      .orderBy("position")
      .preload("image");
    const currentSceneIndex =
      scenes.reduce(
        (acc, cur, index) =>
          cur.id === parseInt(sceneId) ? acc + index + 1 : acc,
        0
      ) - 1; // FIXME just a stupid method for tackling global variables with foreach
    if (currentSceneIndex < 0) {
      Logger.error(`No record found for scene with id: ${sceneId}`);
      return;
    }
    const currentScene = scenes[currentSceneIndex];
    const previousScene = scenes[currentSceneIndex - 1];
    const nextScene = scenes[currentSceneIndex + 1];
    const totalCharacters = new Set(); // total characters in this scene
    const payload = []; // indexes represent positions

    const lineQuery = currentScene.related("lines").query();
    //get line_version_id depending on character_id
    data.forEach((datum) => {
      console.log(datum);
      lineQuery
        .orWhere("character_id", datum.character_id)
        .andWhere("version_id", datum.line_version_id)
        .preload("character", (characterQuery) => {
          characterQuery.preload("image");
        });
    });
    lineQuery.preload("audios", (audioQuery) => {
      data.forEach((datum) => {
        audioQuery
          .orWhere("creator_id", datum.audio_creator_id)
          .andWhere("version_id", datum.audio_version_id)
          .preload("creator");
      });
    });
    const lines = await lineQuery.orderBy("position", "asc");
    lines.forEach((line) => {
      payload.push({
        character: line.character.serialize(),
        line: line.serialize(),
        audio: line.audios[0].serialize(),
      });
    });
    console.log(payload.length, lines.length);
    return view.render("scene/action", { payload });
  }

  public async createNew({ response, auth, params }: HttpContextContract) {
    const playId = params.id;
    const play = await Play.findOrFail(playId);
    const user = await auth.authenticate();
    const newScene = await Scene.create({
      name: "Nouvelle scène",
      position: 2,
      description: "",
      creatorId: user.id,
      playId: play.id,
    });
    return response.redirect().back();
  }

  public async updateName({ request, params }: HttpContextContract) {
    const newSceneName = request.all().newSceneName;
    const scene_id = params.sceneId;
    var scene = await Scene.findOrFail(scene_id);
    scene.name = newSceneName;
    console.log(newSceneName);
    await scene.save();
    return scene;
  }

  public async getCharactersFromPlay(play: Play) {
    var charactersSet = new Set();
    await play.load("scenes", (scenesQuery) => {
      scenesQuery.preload("lines");
    });
    play.scenes.forEach((scene) => {
      scene.lines.forEach((line) => {
        charactersSet.add(line.characterId);
      });
    });
    const charactersArray = Array.from(charactersSet);
    const res = await Character.findMany(charactersArray);
    play.characters = res;
    return res;
  }

  public async getCharactersFromScene(scene: Scene) {
    var charactersSet = new Set();
    await scene.load("lines");
    scene.lines.forEach((el) => {
      charactersSet.add(el.characterId);
    });
    const charactersArray = Array.from(charactersSet);
    const res = await Character.findMany(charactersArray);
    scene.characters = res;
    return res;
  }

  public async edit({ params, view, auth }: HttpContextContract) {
    const scene = await Scene.findOrFail(params.id);

    await scene.load("play", (playQuery) => {
      playQuery.preload("scenes");
    });
    const play = await Play.findOrFail(scene.playId);
    const characterFetcher = new CharacterFetcher;
    scene.characters = await characterFetcher.getCharactersFromScene(scene)
    //scene.characters = await this.getCharactersFromScene(scene);
    await characterFetcher.getCharactersFromPlay(play);

    //get all the lines from a scene
    await scene.load("lines", (linesQuery) => {
      linesQuery.orderBy('lines.position', 'asc')
    })

    return view.render("scene/edit", {
      scene,
      play,
    });
  }

  public async update({ request, params, response }: HttpContextContract) {
    const name = request.all().name;
    const scene_id = params.id;
    var scene = await Scene.findOrFail(scene_id);
    scene.name = name;
    await scene.save();
    return response.redirect().back();
  }

  public async destroy({ response, params }: HttpContextContract) {
    const sceneId = params.id;
    var scene = await Scene.findOrFail(sceneId);
    await scene.delete();
    return response.redirect().back();
  }
}


