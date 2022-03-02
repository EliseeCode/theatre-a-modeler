import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Play from "App/Models/Play";
import Status from "Contracts/enums/Status";
import CharacterFetcher from "../helperClass/CharacterFetcher";
import Role from "Contracts/enums/Role";

export default class PlaysController {
  public dataName = "plays";

  public async index({ view }: HttpContextContract) {
    const plays = await Play.query()
      .preload("scenes", (sceneQuery) => {
        sceneQuery.preload("play").preload("lines", (lineQuery) => {
          lineQuery.preload("character").orderBy("position");
        });
      })
      .preload("groups")
      .preload("creator")
      .preload("image");

    const characterFetcher = new CharacterFetcher();

    for (const play of plays) {
      await characterFetcher.getCharactersFromPlay(play);
      for (const scene of play.scenes) {
        await characterFetcher.getCharactersFromScene(scene);
      }
    }

    return view.render("play/index", { plays, Role });
  }

  public async createNew({ response, auth }: HttpContextContract) {
    const user = await auth.authenticate();
    await Play.create({
      name: "Nouvelle Pièce",
      description: "description",
      creatorId: user.id,
    });
    return response.redirect().back();
  }

  public async create({ view, auth }: HttpContextContract) {
    const user = await auth.authenticate();
    await user.load("groups");
    const status = Status;
    return view.render("play/edit", { user, status });
  }

  public async store({ auth, request, response }: HttpContextContract) {
    const { name, description, publishedGroups } = request.body();
    const user = await auth.authenticate();
    const creatorId = user.id;

    const play = await Play.create({
      name: name || "Pièce sans nom",
      description: description || "",
      creatorId: creatorId,
    });

    await play.save();

    let publishedGroupsArray: string[] = [];
    if (!!publishedGroups) {
      if (typeof publishedGroups == "string") {
        publishedGroupsArray[0] = publishedGroups;
      } else {
        publishedGroupsArray = Object.values(publishedGroups);
      }
      await play.related("groups").attach(publishedGroupsArray);
    }

    return response.redirect().back();
  }

  public async detach({
    bouncer,
    params,
    response,
  }: HttpContextContract) {
    const groupId = params.groupId;
    const playId = params.playId;
    const play = await Play.findOrFail(playId);
    await bouncer.with("PlayPolicy").authorize("link", play, [groupId]);
    await play.related("groups").detach([groupId]);

    return response.redirect().back();
  }
  public async show({ view, params }: HttpContextContract) {
    const data = await Play.findOrFail(params.id);
    return view.render("defaultViews/show", { data, dataName: this.dataName });
  }

  public async edit({ view, auth, params }: HttpContextContract) {
    const user = await auth.authenticate();
    await user.load("groups");
    const status = Status;
    const play = await Play.findOrFail(params.id);
    return view.render("play/edit", { user, status, play });
  }

  public async update({
    bouncer,
    request,
    response,
    params,
  }: HttpContextContract) {
    const { name, description, publishedGroups } = request.body();
    const play_id = params.id;
    var play = await Play.findOrFail(play_id);
    await bouncer.with("PlayPolicy").authorize("update", play);
    play.name = name;
    play.description = description;
    await play.save();
    //Links

    let publishedGroupsArray: any[] = [];
    if (!!publishedGroups) {
      if (typeof publishedGroups == "string") {
        publishedGroupsArray = [publishedGroups];
      } else {
        publishedGroupsArray = Object.values(publishedGroups);
      }
    }
    await bouncer
      .with("PlayPolicy")
      .authorize("link", play, publishedGroupsArray);
    await play.related("groups").sync(publishedGroupsArray);
    return response.redirect().back();
  }

  public async updateName({ request, params }: HttpContextContract) {
    const newPlayName = request.all().newPlayName;
    const play_id = params.playId;
    var play = await Play.findOrFail(play_id);
    play.name = newPlayName;
    await play.save();
    return play;
  }

  public async destroy({ response, params }: HttpContextContract) {
    const playId = params.id;
    var play = await Play.findOrFail(playId);
    await play.delete();
    return response.redirect().back();
  }
}
