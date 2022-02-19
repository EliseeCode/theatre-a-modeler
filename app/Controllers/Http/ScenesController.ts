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
import LineVersionFetcher from "App/Controllers/helperClass/LineVersionFetcher";
import Version from "App/Models/Version";
import User from "App/Models/User";
import AudioFetcher from "App/Controllers/helperClass/AudioFetcher";

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
    const scene = await Scene.findOrFail(params.scene_id);
    const characterFetcher = new CharacterFetcher();
    const lineVersionFetcher = new LineVersionFetcher(scene);
    const audioFetcher = new AudioFetcher(scene);
    await scene.load("creator");
    await scene.load("lines");
    await scene.load("image");
    await scene.load("play");

    scene.characters = await characterFetcher.getCharactersFromScene(scene);

    for (const character of scene.characters) {
      await character.load("image");
      character.versions =
        await lineVersionFetcher.getVersionsFromCharacterOnScene(character); // Why this doesn't automatically updates referenced parameter? Why do we have to restore it in scene.characters?
      for (const version of character.versions) {
        version.doublers =
          await audioFetcher.getDoublersAndAudioVersionsFromLineVersionOnScene(
            version
          ); // FIXME shorten the function name
        /* console.log(
          `${version.name.slice(0, 10)} | ${character.name.slice(
            0,
            10
          )} | ${version.doublers[0].username.slice(0, 10)}`
        ); */
      }
    }

    /* scene.characters.map((character) => {
      // console.log(character.versions[0].name);
      character.serialize({
        fields: {
          omit: ["gender", "created_at", "updated_at", "description"],
        },
        relations: {
          image: {
            fields: {
              pick: ["id", "name", "public_path", "mime_type", "size"],
            },
          },
        },
      });

      return;
    }); */

    return view.render("scene/select", { characters: scene.characters });

    await scene.characters.forEach(async (character) => {
      character.versions =
        await lineVersionFetcher.getVersionsFromCharacterOnScene(character); // Why this doesn't automatically updates referenced parameter? Why do we have to restore it in scene.characters?
      console.log(character.versions);
      character.versions.forEach(async (version) => {
        version.doublers =
          await audioFetcher.getDoublersAndAudioVersionsFromLineVersionOnScene(
            version
          ); // FIXME shorten the function name
        /* console.log(
          `${version.name.slice(0, 10)} | ${character.name.slice(
            0,
            10
          )} | ${version.doublers[0].username.slice(0, 10)}`
        ); */
        return;
        // Property check logs
        version.doublers.map((doubler) => {
          console.log(
            `${doubler.username.slice(0, 10)} | ${character.name.slice(
              0,
              10
            )} | ${doubler.audioVersions[0].id}`
          );
          doubler.audioVersions.map((version) => {
            console.log(`Audio Version: ${version.name} `);
          });
        });
      });
    });
    console.log(scene.characters[1].versions);
    return;
    console.log(
      scene.characters.map((character) => {
        console.log(character.name);
        return character.serialize({
          fields: {
            pick: ["id", "name", "image", "versions"],
          },
        });
      })
    );

    // We want to know which personnage will be animated by who. So, we need to list each possible animator/doubler for a personnage. But! We do accept different improvisations in each line. You can say either "Hi!" or "Hello!". So, we need to track of each version (with its own id & name). And we want to get the doubler ids to know who we're attaching the personnage. But! Again, we do accept multiple audio sets/versions for each line. Like you can say "Hello!" in a rush or calmly.
    // Thus, we have to know character, line version, audio creator and audio version. With this, we can reconstruct a fresh scene, by having a fallback of official version!!!
    /* const payload = {
      2: {
        // character_id
        character: Character,
        lineVersions: {
          3: {
            // line_version_id
            lineVersion: Version, // for using version.name in view
            doublers: {
              // audio creators
              5: {
                // audio_creator_id
                creator: User,
                audioVersions: [1, 3], // audio_version_id
              },
            },
          },
        },
      },
    }; */

    return view.render("scene/select", { payload, user });
  }
  public async index({}: HttpContextContract) {}

  public async create({}: HttpContextContract) {}

  public async store({}: HttpContextContract) {}

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
    const characterFetcher = new CharacterFetcher();
    scene.characters = await characterFetcher.getCharactersFromScene(scene);
    //scene.characters = await this.getCharactersFromScene(scene);
    await characterFetcher.getCharactersFromPlay(play);

    //get all the lines from a scene
    await scene.load("lines", (linesQuery) => {
      linesQuery.orderBy("lines.position", "asc");
    });

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
