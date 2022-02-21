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
    const versionCount = 9;
    const versions = [
      await Version.create({
        id: 1,
        name: "Official",
      }),
      ...(await VersionFactory.createMany(versionCount - 1)),
    ];
    const lineCount = 32;
    const lines = await LineFactory.createMany(lineCount);
    const audioCount = 39;
    //const audios = await AudioFactory.createMany(audioCount);
    const imageCount = 30;
    const images = await ImageFactory.createMany(imageCount);
    for (let i = 0; i < groupCount; i++) {
      await groups[i].related("creator").associate(users[i % userCount]);
    }
    for (let i = 0; i < userCount; i++) {
      await users[i].related("groups").attach({
        [groups[i % groupCount].id]: {
          created_at: DateTime.now().toISO(),
          updated_at: DateTime.now().toISO(),
        },
      });
    }
    for (let i = 0; i < playCount; i++) {
      await plays[i].related("groups").attach({
        [groups[i % groupCount].id]: {
          position: Math.trunc(i / groupCount),
          created_at: DateTime.now().toISO(),
          updated_at: DateTime.now().toISO(),
        },
      });
      await plays[i].related("creator").associate(users[i % userCount]);
    }
    for (let i = 0; i < sceneCount; i++) {
      await scenes[i].related("play").associate(plays[i % playCount]);
      await scenes[i].related("creator").associate(users[i % userCount]);
      await scenes[i].related("image").associate(images[i % imageCount]);
    }
    for (let i = 0; i < characterCount; i++) {
      await characters[i].related("image").associate(images[i % imageCount]);
    }
    for (let i = 0; i < lineCount; i++) {
      await lines[i].related("version").associate(versions[i % versionCount]);
      lines[i].position = Math.trunc(i / sceneCount);
      await lines[i].save();
      await lines[i].related("scene").associate(scenes[i % sceneCount]);
      await lines[i]
        .related("character")
        .associate(characters[i % characterCount]);
      await lines[i].related("creator").associate(users[i % userCount]);
    }
    //for (let i = 0; i < audioCount; i++) {
    //await audios[i].related("creator").associate(users[i % userCount]);
    //await audios[i].related("line").associate(lines[i % lineCount]);
    // await audios[i]
    //   .related("version")
    //   .associate(versions[Math.trunc(i / versionCount)]);
    //}
  }
}
