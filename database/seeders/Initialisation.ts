import BaseSeeder from '@ioc:Adonis/Lucid/Seeder'
import User from 'App/Models/User';
import Version from 'App/Models/Version';

export default class InitialisationSeeder extends BaseSeeder {
  public async run() {
    // Write your database queries inside the run method
    await User.create({
      loginId: "admin",
      password: "adminadmin",
      username: "admin",
      roleId: 4,
    }),
      await Version.create({
        id: 1,
        name: "Official",
        creatorId: 1
      });

  }
}
