import BaseSeeder from "@ioc:Adonis/Lucid/Seeder";
import Audio from "App/Models/Audio";

export default class AudioSeeder extends BaseSeeder {
  public async run() {
    await Audio.createMany([
      {
        name: "Henry IV Act 1 Scene 1 Salut of Prince Henry",
        description: "softly saluts",
        publicPath:
          "https://file-examples-com.github.io/uploads/2017/11/file_example_MP3_5MG.mp3",
        relativePath: "/2017/11/file_example_MP3_5MG.mp3",
        langId: 3,
        creatorId: 1,
        lineId: 1,
        size: 13,
        type: "mp3",
        mimeType: "audio/mpeg",
      },
      {
        name: "Henry IV Act 1 Scene 1 Salut of Sir Walter",
        description: "in a rush",
        publicPath:
          "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-4.mp3",
        relativePath: "/mp3/SoundHelix-Song-4.mp3",
        langId: 3,
        creatorId: 1,
        lineId: 2,
        size: 34,
        type: "mp3",
        mimeType: "audio/mpeg",
      },
      {
        name: "Godot Act 1 Scene 1 Vladimir Asks",
        description: "gently asks",
        publicPath:
          "https://www.learningcontainer.com/wp-content/uploads/2020/02/Kalimba.mp3",
        relativePath: "/uploads/2020/02/Kalimba.mp3",
        langId: 3,
        creatorId: 1,
        lineId: 3,
        size: 22,
        type: "mp3",
        mimeType: "audio/mpeg",
      },
      {
        name: "Godot Act 1 Scene 1 Estragon Replies",
        description: "gently asks",
        publicPath: "https://samplelib.com/lib/preview/mp3/sample-15s.mp3",
        relativePath: "/preview/mp3/sample-15s.mp3",
        langId: 3,
        creatorId: 1,
        lineId: 4,
        size: 22,
        type: "mp3",
        mimeType: "audio/mpeg",
      },
    ]);
  }
}
