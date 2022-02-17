import Factory from "@ioc:Adonis/Lucid/Factory";
import Audio from "App/Models/Audio";
import Character from "App/Models/Character";
import Group from "App/Models/Group";
import Image from "App/Models/Image";
import Line from "App/Models/Line";
import Play from "App/Models/Play";
import Scene from "App/Models/Scene";
import User from "App/Models/User";
import Version from "App/Models/Version";

export const GroupFactory = Factory.define(Group, ({ faker }) => {
  return {
    code: faker.random.alphaNumeric(8),
    description: faker.lorem.sentence(),
    name: faker.lorem.slug(),
    status: "active",
  };
}).build();

export const UserFactory = Factory.define(User, ({ faker }) => {
  return {
    username: faker.internet.userName(),
    email: faker.internet.email(),
    password: faker.internet.password(),
    organisation: faker.commerce.department(),
  };
})
  .relation("groups", () => GroupFactory) // 👈
  .build();

export const PlayFactory = Factory.define(Play, ({ faker }) => {
  return {
    name: faker.hacker.adjective(),
    description: faker.lorem.sentence(),
    status: "active",
  };
})
  .relation("groups", () => GroupFactory) // 👈
  .build();

export const SceneFactory = Factory.define(Scene, ({ faker }) => {
  return {
    status: ["active", "passive"][Math.floor(Math.random() * 2)],
    description: faker.lorem.sentences(3),
    name: faker.lorem.words(3),
  };
})
  .relation("play", () => PlayFactory)
  .build();
export const CharacterFactory = Factory.define(Character, ({ faker }) => {
  return {
    name: faker.internet.userName(),
    gender: ["Male", "Female"][Math.floor(Math.random() * 2)],
  };
})
  .relation("plays", () => PlayFactory) // 👈
  .build();

export const LineFactory = Factory.define(Line, ({ faker }) => {
  return {
    status: ["active", "passive"][Math.floor(Math.random() * 2)],
    text: faker.lorem.sentences(3),
  };
})
  .relation("scene", () => SceneFactory)
  .build();
export const AudioFactory = Factory.define(Audio, ({ faker }) => {
  return {
    mimeType: ["audio/mp3", "audio/mpeg", "audio/wav"][
      Math.floor(Math.random() * 3)
    ],
    type: ["mp3", "mpeg", "wav"][Math.floor(Math.random() * 3)],
    name: faker.lorem.words(3),
    size: faker.datatype.number(150000),
    publicPath: [
      "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-4.mp3",
      "https://file-examples-com.github.io/uploads/2017/11/file_example_MP3_5MG.mp3",
      "https://www.learningcontainer.com/wp-content/uploads/2020/02/Kalimba.mp3",
      "https://samplelib.com/lib/preview/mp3/sample-15s.mp3",
    ][Math.floor(Math.random() * 4)],
    relativePath: faker.internet.url(),
    description: faker.lorem.sentences(3),
    creatorId: 1,
    lineId: 1,
    versionId: 1,
  };
}).build();

export const VersionFactory = Factory.define(Version, ({ faker }) => {
  return {
    name: faker.lorem.words(3),
  };
}).build();

export const ImageFactory = Factory.define(Image, ({ faker }) => {
  return {
    mimeType: ["image/png", "image/jpg", "image/gif"][
      Math.floor(Math.random() * 3)
    ],
    type: ["png", "jpg", "gif"][Math.floor(Math.random() * 3)],
    name: faker.lorem.words(3),
    size: faker.datatype.number(150000),
    publicPath: faker.internet.url(),
    relativePath: faker.internet.url(),
    description: faker.lorem.sentences(3),
    creatorId: 1,
  };
}).build();
