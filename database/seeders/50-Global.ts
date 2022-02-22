import BaseSeeder from "@ioc:Adonis/Lucid/Seeder";
import Character from "App/Models/Character";
import User from "App/Models/User";
import Version from "App/Models/Version";
import Role from "Contracts/enums/Role";
import {
  AudioFactory,
  CharacterFactory,
  GroupFactory,
  ImageFactory,
  LineFactory,
  PlayFactory,
  SceneFactory,
  UserFactory,
  VersionFactory,
} from "Database/factories";
import { DateTime } from "luxon";

export default class UserSeeder extends BaseSeeder {
  static createMany: any;
  public async run() {
    await Version.create({
      id: 1,
      name: "Official",
    });
    const groupCount = 4;
    const groups = await GroupFactory.createMany(groupCount);
    const userCount = 15;
    const users = [
      await User.create({
        loginId: "admin",
        password: "adminadmin",
        username: "admin",
        roleId: 4,
      }),
      ...(await UserFactory.createMany(userCount - 1)),
    ];
    const playCount = 7;
    const plays = await PlayFactory.createMany(playCount);
    const sceneCount = 5;
    const scenes = await SceneFactory.createMany(sceneCount);
    const characterCount = 11;
    const characters = [
      await Character.create({
        id: 1,
        name: "Didascalie",
      }),
      ...(await CharacterFactory.createMany(characterCount - 1)),
    ];
    const lineCount = 32;
    const lines = await LineFactory.with("version", 1).createMany(lineCount);
    const audioCount = 39;
    const audios = await AudioFactory.with("version", 1).createMany(audioCount);
    const imageCount = 30;
    const images = await ImageFactory.createMany(imageCount);
    for (let i = 0; i < groupCount; i++) {
      groups[i].creatorId = users[i % userCount].id;
      await groups[i].related("creator").associate(users[i % userCount]);
      await groups[i].save();
    }
    for (let i = 0; i < userCount; i++) {
      await users[i].related("groups").attach({
        [groups[i % groupCount].id]: {
          created_at: DateTime.now().toISO(),
          updated_at: DateTime.now().toISO(),
        },
      });
      await users[i].save();
    }
    for (let i = 0; i < playCount; i++) {
      await plays[i].related("groups").attach({
        [groups[i % groupCount].id]: {
          position: Math.trunc(i / groupCount),
          created_at: DateTime.now().toISO(),
          updated_at: DateTime.now().toISO(),
        },
      });
      plays[i].creatorId = users[i % userCount].id;
      await plays[i].related("creator").associate(users[i % userCount]);
      await plays[i].save();
    }
    for (let i = 0; i < sceneCount; i++) {
      scenes[i].imageId = images[i % imageCount].id;
      scenes[i].playId = plays[i % playCount].id;
      scenes[i].creatorId = users[i % userCount].id;
      await scenes[i].related("play").associate(plays[i % playCount]);
      await scenes[i].related("creator").associate(users[i % userCount]);
      await scenes[i].related("image").associate(images[i % imageCount]);
      await scenes[i].save();
    }
    for (let i = 0; i < characterCount; i++) {
      characters[i].imageId = images[i % imageCount].id;
      await characters[i].related("image").associate(images[i % imageCount]);
      await characters[i].save();
    }
    for (let i = 0; i < lineCount; i++) {
      // await lines[i].related("version").associate(versions[i % versionCount]);
      lines[i].position = Math.trunc(i / sceneCount);
      lines[i].sceneId = scenes[i % sceneCount].id;
      lines[i].characterId = characters[i % characterCount].id;
      lines[i].creatorId = users[i % userCount].id;
      await lines[i].related("scene").associate(scenes[i % sceneCount]);
      await lines[i]
        .related("character")
        .associate(characters[i % characterCount]);
      await lines[i].related("creator").associate(users[i % userCount]);
      await lines[i].save();
    }
    for (let i = 0; i < audioCount; i++) {
      audios[i].creatorId = users[i % userCount].id;
      audios[i].lineId = lines[i % lineCount].id;
      await audios[i].related("creator").associate(users[i % userCount]);
      await audios[i].related("line").associate(lines[i % lineCount]);
      await audios[i].save();
      // await audios[i].related("version").associate(versions[i % versionCount]);
    }
    for (let i = 0; i < imageCount; i++) {
      images[i].creatorId = users[i % userCount].id;
      await images[i].related("creator").associate(users[i % userCount]);
      await images[i].save();
    }
  }
}
