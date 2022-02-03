import BaseSeeder from "@ioc:Adonis/Lucid/Seeder";
import Scene from "App/Models/Scene";

export default class SceneSeeder extends BaseSeeder {
  public async run() {
    // Write your database queries inside the run method
    await Scene.createMany([
      {
        name: "Act 1 Scene 1",
        position: 0,
        description: "Une scene d'introduction des personnages",
        creatorId: 1,
        playId: 1,
      },
      {
        name: "Act 1 Scene 2",
        position: 1,
        description: "Une scene avec des personnages",
        creatorId: 1,
        playId: 1,
      },
      {
        name: "Act 1 Scene 3",
        position: 2,
        description: "Une troisieme scene avec des personnages",
        creatorId: 1,
        playId: 1,
      },
      {
        name: "Intro",
        position: 0,
        description: "Ou est Godot?",
        creatorId: 1,
        playId: 2,
      },
      {
        name: "2e partie",
        position: 1,
        description: "Godot...",
        creatorId: 1,
        playId: 2,
      },
    ]);
  }
}
