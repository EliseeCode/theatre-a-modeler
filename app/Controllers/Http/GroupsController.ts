import { HttpContextContract } from "@ioc:Adonis/Core/HttpContext";
import Group from "App/Models/Group";
import Character from "App/Models/Character";
import Logger from "@ioc:Adonis/Core/Logger";
import Role from "Contracts/enums/Role";
import CharacterFetcher from "../helperClass/CharacterFetcher";

export default class GroupsController {
  public async index({}: HttpContextContract) {}

  public async create({ view }: HttpContextContract) {
    return view.render("group/edit");
  }

  public async store({ auth, request, response }: HttpContextContract) {
    const user = await auth.authenticate();
    const creatorId = user.id;
    var isValidCode = false;
    var code = "";
    while (!isValidCode) {
      code = this.generateCode();
      Logger.info("test code " + code);
      isValidCode = (await Group.query().where("code", code).count)
        ? true
        : false;
    }
    Logger.info("code will be " + code);

    const group = await Group.create({
      name: request.all().name || "Groupe sans nom",
      color: request.all().color || "#ff0000",
      description: request.all().description,
      creatorId: creatorId,
      code: code,
    });
    Logger.info("Group created");
    await user
      .related("groups")
      .save(group, undefined, { role_id: Role.TEACHER });

    return response.redirect().toRoute("/dashboard");
  }

  public async show({ params, view, auth, bouncer }: HttpContextContract) {
    const group = await Group.findOrFail(params.id);
    const user = await auth.authenticate();
    await bouncer.with("GroupPolicy").authorize("view", group);
    await user.load("groups");
    if (group) {
      await group.load("plays", (playQuery) => {
        playQuery
          .preload("scenes", (sceneQuery) => {
            sceneQuery.preload("lines", (lineQuery) => {
              lineQuery.preload("character");
            });
          })
          .preload("creator")
          .preload("groups", (groupQuery) => {
            groupQuery.whereIn(
              "groups.id",
              user.groups.map((el) => el.id)
            );
          });
      });

<<<<<<< HEAD

      const characterFetcher = new CharacterFetcher;
=======
      const characterFetcher = new CharacterFetcher();
>>>>>>> 137252bf87631ea725f8ff77a1bddd6e662c8407

      for (const play of group.plays) {
        await characterFetcher.getCharactersFromPlay(play);
        for (const scene of play.scenes) {
          await characterFetcher.getCharactersFromScene(scene);
        }
      }
<<<<<<< HEAD


=======
>>>>>>> 137252bf87631ea725f8ff77a1bddd6e662c8407

      // //create a set of character's id and loop over each line to populate the set.
      // var characterIdsByPlay = Array();
      // group.plays.forEach(async (play) => {
      //   var charactersSet = new Set();
      //   play.scenes.forEach((scene) => {
      //     scene.lines.forEach((line) => charactersSet.add(line.characterId))
      //   })
      //   //transform the set in Array and get Characters from those.
      //   var characterIds = Array.from(charactersSet);
      //   console.log(play.id, characterIds);
      //   characterIdsByPlay[play.id] = characterIds;
      // });
      // console.log(characterIdsByPlay);
      // const charactersByPlay = characterIdsByPlay.map(async (el) => {
      //   var a = await Character.findMany(el);
      //   return a;
      // });
      // console.log(charactersByPlay);
      return view.render("group/show", { group, user, Role });
    }
  }

  public async edit({ view, params }: HttpContextContract) {
    const groupId = params.id;
    var group = await Group.findOrFail(groupId);
    return view.render("group/edit", { ...group.serialize() });
  }

  public async update({
    auth,
    params,
    response,
    request,
    bouncer,
  }: HttpContextContract) {
    //const user = await auth.authenticate();
    const groupId = params.id;
    const group = await Group.findOrFail(groupId);
    await bouncer.with("GroupPolicy").authorize("update", group);
    group.name = request.all().name || "Groupe sans nom";
    group.color = request.all().color || "#ff0000";
    group.description = request.all().description;
    group.save();
    Logger.info("Group updated");
    return response.redirect().toRoute("/dashboard");
  }

  public async destroy({ response, params, bouncer }: HttpContextContract) {
    const groupId = params.id;
    var group = await Group.findOrFail(groupId);
    await bouncer.with("GroupPolicy").authorize("delete", group);
    await group.delete();
    return response.redirect().back();
  }

  public generateCode() {
<<<<<<< HEAD
    var characters = 'ABCDEFGHJKLMNOPRSTUVWXZ0123456789';
    var result = ""
=======
    var characters = "abcdefghjklmnpqrstuvwxyz0123456789";
    var result = "";
>>>>>>> 137252bf87631ea725f8ff77a1bddd6e662c8407
    var charactersLength = characters.length;

    for (var i = 0; i < 4; i++) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
  }

  public async join({ params, auth, response, request }: HttpContextContract) {
    Logger.info("join");
    if (auth.isGuest) {
      return response.redirect().toRoute("/login");
    }
    const user = await auth.authenticate();
    Logger.info("code1" + params?.code);
    Logger.info("code2" + request.all().code);
    const code = params?.code || request.all().code;
    Logger.info("code" + code);
    console.log(3);
    var group = await Group.findByOrFail("code", code);
    console.log(group);
    await user
      .related("groups")
      .save(group, undefined, { role_id: Role.STUDENT });

    return response.redirect().back();
  }

  public async leave({ params, auth, response }: HttpContextContract) {
    if (auth.isGuest) {
      return response.redirect().toRoute("/login");
    }
    const user = await auth.authenticate();
    const groupId = params.id;
    await user.related("groups").detach([groupId]);
    return response.redirect().back();
  }
}
