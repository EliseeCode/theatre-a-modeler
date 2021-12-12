import BaseSeeder from '@ioc:Adonis/Lucid/Seeder'
import User from 'App/Models/User'

export default class UserSeeder extends BaseSeeder {
  static createMany: any
  public async run () {
    // Write your database queries inside the run method
   
      await User.createMany([
        {
          name: 'elisee',
          email: 'reclus.elisee@gmail.com',
          password: '131313',
          role: 'admin',
          organisation: 'NDS',
        },
        {
          name: 'admin1',
          email: 'admin@test.com',
          password: 'passwordAdmin',
          role: 'admin',
          organisation: 'NDS',
        },
        {
          name: 'tester1',
          email: 'tester1@test.com',
          password: 'passwordtester',
          organisation: 'SP',
        },
        {
          name: 'tester2',
          email: 'tester2@test.com',
          password: 'passwordtester',
          organisation: 'SJ',
        }
      ])
    }
  
  
}
