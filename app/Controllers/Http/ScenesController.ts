import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Scene from "App/Models/Scene";
import Play from "App/Models/Play";
import Logger from "@ioc:Adonis/Core/Logger";
import Group from "App/Models/Group";
import Image from "App/Models/Image";
import Character from "App/Models/Character";
import Line from "App/Models/Line";

export default class ScenesController {
  public async select({ params, view }: HttpContextContract) {
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
      .preload("image")
      .preload("lines", (lineQuery) =>
        lineQuery
          .preload("audios", (audioQuery) => {
            audioQuery.preload("creator");
          })
          .preload("version")
          .preload("character")
      );
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
    const payload = {};
    currentScene.lines.forEach((line) => {
      console.log(`Going through line with id of: ${line.id}`);
      totalCharacters.add(line.character.id);
      const lineCharacter = line?.character; // fallback value of 1 is for "didascalie"
      const lineVersion = line?.version?.id ?? "official";

      console.log(`Added to the total characters...`);
      if (!payload[lineCharacter?.id ?? 1]) {
        console.log(
          `No record found for character with id of: ${
            lineCharacter?.id ?? 1
          } on the payload...`
        );
        payload[lineCharacter?.id ?? 1] = { character: null, lines: {} };
      }

      payload[lineCharacter?.id ?? 1].character = lineCharacter.serialize();

      if (!payload[lineCharacter?.id ?? 1].lines[lineVersion]) {
        console.log(
          `No record found for line with version id of: ${lineVersion} on the payload...`
        );
        payload[lineCharacter?.id ?? 1].lines[lineVersion] = {
          line: null,
          doublers: {},
        };
      }
      payload[lineCharacter?.id ?? 1].lines[lineVersion].line =
        line.serialize();

      line.audios.forEach((audio) => {
        const audioCreator = audio?.creator; // FIXME not sure for this...
        if (
          !payload[lineCharacter?.id ?? 1].lines[lineVersion].doublers[
            audioCreator.id ?? "public"
          ]
        ) {
          console.log(
            `No record found for audio with creator id of: ${audioCreator} on the payload...`
          );
          payload[lineCharacter?.id ?? 1].lines[lineVersion].doublers[
            audioCreator.id ?? "public"
          ] = { creator: null, audios: new Set() };
        }
        payload[lineCharacter?.id ?? 1].lines[lineVersion].doublers[
          audioCreator.id ?? "public"
        ].creator = audioCreator.serialize();

        payload[lineCharacter?.id ?? 1].lines[lineVersion].doublers[
          audioCreator.id ?? "public"
        ].audios.add(audio.versionId);
      });
    });
    return view.render("scene/select.edge", { payload });
  }
  public async index({}: HttpContextContract) {}

  public async create({}: HttpContextContract) {}

  public async store({}: HttpContextContract) {}

  public async action({
    request,
    params,
    view,
    response,
  }: HttpContextContract) {
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
  public async show({ request, params, view }: HttpContextContract) {
    console.log(request.body());

    return;
    const sceneInst = await Scene.query()
      .where("id", params.scene_id)
      .preload("lines", (lineQuery) => {
        lineQuery.orderBy("position", "asc").preload("character");
      });
    const scene = sceneInst[0].serialize();

    var userByCharacter = {
      1: 1,
      2: 2,
    };

    const sceneInst2 = await Scene.query().preload("lines", (lineQuery) => {
      for (let characterId in userByCharacter) {
        lineQuery
          .where("characterId", characterId)
          .preload("audios", (audioQuery) => {
            audioQuery.where("creator_id", userByCharacter[characterId]);
          });
      }
    });

    const playId = params.play_id;
    const play = await (await Play.findOrFail(playId)).serialize();
    return view.render("scene/show", { scene, play, sceneInst2 });
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

  public async edit({ params, view }: HttpContextContract) {
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
      return;
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

    return view.render("scene/edit", { payload });
  }

  public async update({}: HttpContextContract) {}

  public async destroy({ response, params }: HttpContextContract) {
    const sceneId = params.id;
    var scene = await Scene.findOrFail(sceneId);
    await scene.delete();
    return response.redirect().back();
  }
}
