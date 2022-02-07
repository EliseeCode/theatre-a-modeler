import BaseSeeder from '@ioc:Adonis/Lucid/Seeder'
import Line from 'App/Models/Line';

export default class LineSeeder extends BaseSeeder {
  public async run () {
    // Write your database queries inside the run method
    await Line.createMany([
      {
        text: 'Hello Sir',
        position : 0,
        creatorId:1,
        sceneId:1,
        characterId:1
      },
      {
        text: 'Hello Majesty',
        position : 1,
        creatorId:1,
        sceneId:1,
        characterId:2
      },
      {
        text: 'Where is Godot?',
        position : 0,
        creatorId:1,
        sceneId:4,
        characterId:2
      },
      {
        text: 'Dont know',
        position : 1,
        creatorId:1,
        sceneId:4,
        characterId:1
      }
    ]);
  }
}
