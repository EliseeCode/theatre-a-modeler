import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Scene from "App/Models/Scene";
import Play from "App/Models/Play";
import Logger from "@ioc:Adonis/Core/Logger";
import Group from "App/Models/Group";
import Image from "App/Models/Image";
import Character from "App/Models/Character";
import Status from "Contracts/enums/Status";
import Line from "App/Models/Line";

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
          `No record found for character with id of: ${
            lineCharacter?.id ?? 1
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
  public async index({}: HttpContextContract) {}

  public async create({}: HttpContextContract) {}

  public async store({}: HttpContextContract) {}

  public async show({ request, params, view, response }: HttpContextContract) {
    const data = JSON.parse(request.input("data"));

    const currentGroup = await Group.findOrFail(params.group_id);
    const currentPlay = (
      await currentGroup
        .related("plays")
        .query()
        .where("play_id", params.play_id)
        .preload("characters")
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
          cur.id === parseInt(params.scene_id) ? acc + index + 1 : acc,
        0
      ) - 1; // FIXME just a stupid method for tackling global variables with foreach
    if (currentSceneIndex < 0) {
      Logger.error(`No record found for scene with id: ${params.scene_id}`);
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
    const play = await Play.findOrFail(params.id);
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
  public async edit({ params, view, auth }: HttpContextContract) {
    const user = await auth.authenticate();
    const scene = await Scene.findOrFail(params.id);
    await scene.load("play", (playQuery) => {
      playQuery.preload("scenes");
    });

    await scene.load("play", (playQuery) => {
      playQuery.preload("scenes", (scenesQuery) => {
        scenesQuery.preload("lines");
      });
    });
    var charactersSet = new Set();
    scene.play.scenes.forEach((scene) => {
      scene.lines.forEach((line) => charactersSet.add(line.characterId));
    });
    const characterIds = Array.from(charactersSet);
    const characters = await Character.findMany(characterIds);

    await scene.load("lines", (lineQuery) => {
      lineQuery.orderBy("position").preload("character", (characterQuery) => {
        characterQuery.preload("image");
      });
    });
    return view.render("scene/edit", {
      scene,
      characters,
    });

    const currentGroup = await Group.findOrFail(params.group_id);
    const currentPlay = (
      await currentGroup
        .related("plays")
        .query()
        .where("play_id", params.play_id)
        .preload("characters")
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
          cur.id === parseInt(params.id) ? acc + index + 1 : acc,
        0
      ) - 1; // FIXME just a stupid method for tackling global variables with foreach

    if (currentSceneIndex < 0) {
      Logger.error(`No record found for scene with id: ${params.id}`);
      const totalCharacters = currentPlay.characters.map((character) =>
        character.serialize()
      ); // on this play
      return view.render("scene/edit", {
        scene,
        payload: {
          totalCharacters: totalCharacters,
          lineData: null,
        },
      });
    }
    const currentScene = scenes[currentSceneIndex];
    const previousScene = scenes[currentSceneIndex - 1];
    const nextScene = scenes[currentSceneIndex + 1];
    /* const scene = (
      await currentPlay
        .related("scenes")
        .query()
        .where("id", params.id)
        .preload("lines")
        .preload("creator")
        .preload("image")
    )[0]; */
    const lines = await currentScene
      .related("lines")
      .query()
      .whereNull("version_id") // for official line data
      .orderBy("position", "asc")
      .preload("character");
    /*type lineData = {
      // character: Character;
      line: Line;
    };*/
    type payload = {
      playName: string;
      sceneName: string;
      sceneDescription: string;
      sceneImage: Image;
      previousScene: Scene;
      nextScene: Scene;
      lineData: Line[]; // lineData[];
      totalCharacters: any[];
    };

    const payload: payload = {
      totalCharacters: Array.from(
        currentPlay.characters.map((character) => character.serialize())
      ),
      playName: currentPlay.name,
      sceneName: currentScene.name,
      sceneDescription: currentScene.description,
      sceneImage: currentScene.image,
      previousScene: previousScene,
      nextScene: nextScene,
      lineData: [], // ordered by position
    };
    lines.forEach((line) => {
      payload.lineData.push(/*character: line.character,*/ line); // FIXME it can be unnec. for character property
    });
    console.log(payload.totalCharacters);
    /* await groups.forEach(async (group) => {
      (
        await group
          .related("plays")
          .query()
          .where("id", params.id)
          .preload("scenes")
      ).forEach(async (play) => {
        await play
          .related("scenes")
          .query()
          .where("id", params.id)
          .preload("creator")
          .preload("play")
          .preload("image")
          .preload("lines");
      });
    }); */
    /* .preload("plays", async (playsQuery) => {
        await playsQuery
          .where("id", params.play_id)
          .preload("scenes", async (scenesQuery) => {
            await scenesQuery
              .where("id", params.id)
              .preload("play")
              .preload("creator")
              .preload("image")
              .preload("lines");
          });
      }); */

    return view.render("scene/edit", { payload, user, status: Status });
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

// For payload in /select
/* 
We need this structure to store different alternative lines per character upon different audio versions per creator.
1 (Lux): {
  character: Lux,
  lines: {
    1 (Official): {
      line: Line,
      doublers: {
        4 (Renekton) : {
          creator: Renekton,
          audios: [2, 3]
        }
        9 (Aphelios) : {
          creator: Aphelios,
          audios: [6, 12]
        }
      }
    },
    7: {
      line: Line,
      doublers: {
      12 (Master Yi): {
        creator: Master Yi,
        audios: [1]
      }
      }
    }
  }
}

paylod = {
  line_character_id: {
    character: Character,
    lines: {
      line_version_id: { // Relation with versions table
        line: Line,
        doublers: {
          audio_creator_id: {
            creator: User,
            audios: [audio_version_ids] // Audios can be grouped by version id
          }
        },
      },
    },
  },
}; */
