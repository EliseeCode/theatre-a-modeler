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
import { ModelObject } from "@ioc:Adonis/Lucid/Orm";

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
    // We want to know which personnage will be animated by who. So, we need to list each possible animator/doubler for a personnage. But! We do accept different improvisations in each line. You can say either "Hi!" or "Hello!". So, we need to track of each version (with its own id & name). And we want to get the doubler ids to know who we're attaching the personnage. But! Again, we do accept multiple audio sets/versions for each line. Like you can say "Hello!" in a rush or calmly.
    // Thus, we have to know character, line version, audio creator and audio version. With this, we can reconstruct a fresh scene, by having a fallback of official version!!!

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
        console.log(version);
        version.doublers =
          await audioFetcher.getDoublersAndAudioVersionsFromLineVersionOnScene(
            version
          ); // FIXME shorten the function name
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
  }
  public async index({}: HttpContextContract) {}

  public async create({}: HttpContextContract) {}

  public async store({}: HttpContextContract) {}

  public async action({
    request,
    params,
    view,
    response,
    auth,
  }: HttpContextContract) {
    console.log(request.body());
    const user = await auth.authenticate();
    const scene = await Scene.findOrFail(params.scene_id);
    const versions = request.body()?.versions ?? [];
    const lineVersionFetcher = new LineVersionFetcher(scene);
    // versions: ["(character_id)-(line_version_id)-(audio_version_id)"]
    // if line_version_id = 0; create alternative entry/version
    // if doubler_id = record; send a toBeRecorded bool
    // if doubler_id = robot; send a robotized bool
    // if audio_version_id = 0; send a toBeRecorded bool
    if (!versions) return;
    const finalQuery = scene.related("lines").query(); // We are iterating over the versions and "cooking" a global query, where in the end, we'll have the whole line structure ordered by position.
    const versionRegex = /^[1-9]\d*-\d+-([1-9]+||record|robot)-\d+$/;
    const cookedLines: ModelObject[] = [];

    for (const version of versions) {
      if (!versionRegex.test(version))
        return response.internalServerError(
          "Error in version parse... Please cook it more! :)"
        );
      let [characterID, lineVersionID, doublerID, audioVersionID] =
        version.split("-");
      console.log(
        `Character ID: ${characterID}\nLine Version ID:${
          lineVersionID == 0 ? "Alternative Text" : lineVersionID
        }\nDoubler ID:${
          typeof doublerID === "string" ? doublerID.toUpperCase() : doublerID
        }\nAudio Version ID:${
          audioVersionID == 0 ? "To be recorded" : audioVersionID
        }\n`
      );
      const character = await Character.findOrFail(characterID);
      const characterVersions = (
        await lineVersionFetcher.getVersionsFromCharacterOnScene(character)
      ).map((version) => version.id);
      const versionExistsOnCharacter = characterVersions.includes(
        parseInt(lineVersionID)
      ); // If not a specific version exists on character, we'll query the official version...
      console.log(versionExistsOnCharacter);
      let [isAlternative, isRobotized, toBeRecorded] =
        Array<boolean>(3).fill(false);

      isRobotized = doublerID == "robot";
      toBeRecorded = doublerID == "record" && !parseInt(audioVersionID);

      if (lineVersionID === "0") {
        console.log("So, you wanna create an alternative? OK, go on!\n");
        // lineVersionID = 1; // serving to user the official lines...
        isAlternative = true;
        isRobotized = false;
      }

      const lines = await (
        await scene
          .related("lines")
          .query()
          .where("character_id", parseInt(characterID))
          .unless(
            versionExistsOnCharacter,
            (ifquery) => {
              console.log(
                `are you lost? no version id like this: ${lineVersionID} on ${characterID}...`
              );
              ifquery.where("version_id", 1);
            },
            (ifquery) => {
              console.log("there exists a version id on line!");
              ifquery.where("version_id", parseInt(lineVersionID));
            }
          )
          .preload("character", (characterQuery) => {
            characterQuery.preload("image");
          })
          .preload("audios", (audioQuery) => {
            audioQuery
              .where("creator_id", doublerID)
              .where("version_id", audioVersionID);
          })
      ).map((line) => {
        line.isAlternative = isAlternative;
        line.isRobotized = isRobotized;
        line.toBeRecorded = toBeRecorded;
        return line.serialize({
          fields: {
            pick: [
              "id",
              "character_id",
              "version_id",
              "position",
              "isAlternative",
              "text",
              "isRobotized",
              "toBeRecorded",
            ],
          },
          relations: {
            character: {
              fields: {
                omit: ["created_at", "updated_at", "versions"],
              },
              relations: {
                image: {
                  fields: {
                    omit: ["type", "relative_path", "created_at", "updated_at"],
                  },
                },
              },
            },
          },
        });
      });

      if (!lines.length)
        return response.internalServerError(
          "Scene data is corrupted. No official line version exists..."
        );

      cookedLines.push(...lines);
    }

    cookedLines.sort(
      (currLine, nextLine) => currLine.position - nextLine.position
    );
    const cookedAudios: any[] = [];
    cookedLines.map((line) => {
      cookedAudios.push(line.audios.length ? line.audios[0].public_path : null);
    });

    return view.render("scene/action", {
      lines: cookedLines,
      audios: cookedAudios,
    });
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

  public async createNew({
    bouncer,
    response,
    auth,
    params,
    request,
  }: HttpContextContract) {
    const playId = params.id;
    const play = await Play.findOrFail(playId);
    await bouncer.with("PlayPolicy").authorize("update", play);
    await play.load("scenes");
    const user = await auth.authenticate();
    const name = request.body().name || "Scène sans nom";
    await Scene.create({
      name: name,
      position: play.scenes.length,
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
      linesQuery
        .preload("character", (characterQuery) => {
          characterQuery.preload("image");
        })
        .orderBy("lines.position", "asc");
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
