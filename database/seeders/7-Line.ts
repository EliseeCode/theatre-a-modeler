import BaseSeeder from '@ioc:Adonis/Lucid/Seeder'
import Line from 'App/Models/Line';

export default class LineSeeder extends BaseSeeder {
  public async run () {
    // Write your database queries inside the run method
    await Line.createMany([
      {
        text: 'Hello Sir',
        position : 0,
        creator_id:1,
        scene_id:1,
        character_id:1
      },
      {
        text: 'Hello Majesty',
        position : 1,
        creator_id:1,
        scene_id:1,
        character_id:2
      },
      {
        text: 'Where is Godot?',
        position : 0,
        creator_id:1,
        scene_id:4,
        character_id:2
      },
      {
        text: 'Dont know',
        position : 1,
        creator_id:1,
        scene_id:4,
        character_id:1
      }
    ]);
  }
}
