import BaseSeeder from '@ioc:Adonis/Lucid/Seeder'
import Play from 'App/Models/Play';

export default class PlaySeeder extends BaseSeeder {
  public async run () {
    // Write your database queries inside the run method
    await Play.createMany([
      {
        name: 'Henri IV',
        description: "une piece de célèbre",
        creatorId:1
      },
      {
        name: 'En attendant Godot',
        description: "l'ennuie eternel",
        creatorId:1
      },
      {
        name: 'Le Cid',
        description: "une autre piece célèbre",
        creatorId:2
      }
    ]);
  }
}
