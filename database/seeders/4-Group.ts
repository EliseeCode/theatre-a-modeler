import BaseSeeder from '@ioc:Adonis/Lucid/Seeder'
import Group from 'App/Models/Group';

export default class GroupSeeder extends BaseSeeder {
  public async run () {
    // Write your database queries inside the run method
    await Group.createMany([
      {
        name: 'HAZ-A',
        creator_id: 1
      },
      {
        name: 'HAZ-B',
        creator_id: 1
      },
      {
        name: 'HAZ-C',
        creator_id: 1
      }
    ]);
  }
}
