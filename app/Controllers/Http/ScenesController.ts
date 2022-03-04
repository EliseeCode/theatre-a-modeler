import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Scene from "App/Models/Scene";
import Play from "App/Models/Play";
import Line from "App/Models/Line";
import CharacterFetcher from "App/Controllers/helperClass/CharacterFetcher";
import LineVersionFetcher from "App/Controllers/helperClass/LineVersionFetcher";
import AudioFetcher from "App/Controllers/helperClass/AudioFetcher";

export default class ScenesController {
  public async show({ params, view }: HttpContextContract) {
    const scene = await Scene.findOrFail(params.id);
    //get other scene from play to navigate between scene of the same play
    await scene.load("play", (playQuery) => {
      playQuery.preload("scenes");
    });
    //get character from scene
    const characterFetcher = new CharacterFetcher();
    await characterFetcher.getCharactersFromScene(scene);

    //get all the lines from a scene
    await scene.load("lines", (linesQuery) => {
      linesQuery
        .preload("version")
        .preload("character", (characterQuery) => {
          characterQuery.preload("image");
        })
        .orderBy("lines.position", "asc");
    });

    const lines = await Line.query()
      .preload("character", (characterQuery) => {
        characterQuery.preload("image");
      })
      .preload("version")
      .where("lines.scene_id", scene.id)
      .distinct("lines.version_id", "lines.character_id");

    const linesJSON = lines.map((line) => {
      console.log(line);
      return line.serialize();
    });

    console.log(linesJSON[0]);

    const sceneLength = scene.lines[scene.lines.length - 1].position + 1;

    //Make Character[].versions[]
    const characters = linesJSON.reduce(function (acc, cur) {
      if (cur.character == null) {
        return acc;
      }
      if (
        acc
          .map((char) => {
            return char.id;
          })
          .includes(cur.character.id)
      ) {
        //push version if character is already in accumulator
        acc
          .filter((char) => {
            return char.id == cur.character.id;
          })[0]
          .versions.push(cur.version);
      } else {
        cur.character.versions = [cur.version];
        acc.push(cur.character);
      }
      return acc;
    }, []);

    //const charactersArray = Object.values(characters);
    console.log(sceneLength);
    return view.render("scene/show", {
      scene,
      characters,
      sceneLength,
    });
  }

  private async selectCharacters(scene_id) {
    // We want to know which personnage will be animated by who. So, we need to list each possible animator/doubler for a personnage. But! We do accept different improvisations in each line. You can say either "Hi!" or "Hello!". So, we need to track of each version (with its own id & name). And we want to get the doubler ids to know who we're attaching the personnage. But! Again, we do accept multiple audio sets/versions for each line. Like you can say "Hello!" in a rush or calmly.
    // Thus, we have to know character, line version, audio creator and audio version. With this, we can reconstruct a fresh scene, by having a fallback of official version!!!

    // FIXME if we don't serialize objects, we're risking exposing our appRoot

    const scene = await Scene.findOrFail(scene_id);
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
            version,
            character
          ); // FIXME shorten the function name
      }
    }
    const serializedCharacters = scene.characters.map((character) => {
      // console.log(character.versions[0].name);
      const versions = character.versions.map((version) => {
        const serializedVersion = version.serialize();
        serializedVersion.doublers = serializedVersion.doublers.map(
          (doubler) => {
            const serializedDoubler = doubler.serialize();
            console.log(serializedDoubler.username);
            serializedDoubler.audioVersions.map((audioVersion) => {
              return audioVersion.serialize();
            });
            return serializedDoubler;
          }
        );
        return serializedVersion;
      });
      const serializedCharacter = character.serialize({
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
      serializedCharacter.versions = versions;
      return serializedCharacter;
    });

    return serializedCharacters;
  }

  public async select({ params, view }: HttpContextContract) {
    return view.render("scene/test", {
      characters: await this.selectCharacters(params.scene_id),
    });
  }

  public async index({ }: HttpContextContract) { }

  public async create({ }: HttpContextContract) { }

  public async store({ }: HttpContextContract) { }

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
    const scene = await Scene.create({
      name: name,
      position: play.scenes.length,
      description: "",
      creatorId: user.id,
      playId: play.id,
    });
    return response.redirect("/scene/" + scene.id + "/edit");
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
  public async lines({ params }: HttpContextContract) {
    const sceneId = params.sceneId;
    const versionId = params.versionId;
    const scene = await Scene.findOrFail(sceneId);
    //get all the lines from a scene and version
    const lines = await Line.query()
      .where('lines.version_id', versionId)
      .andWhere('lines.scene_id', sceneId)
      .preload("character", (characterQuery) => {
        characterQuery.preload("image");
      })
      .orderBy("lines.position", "asc");

    const characterFetcher = new CharacterFetcher();
    const characters = await characterFetcher.getCharactersFromScene(scene);
    return { lines, characters }
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
