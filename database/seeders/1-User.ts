import BaseSeeder from '@ioc:Adonis/Lucid/Seeder'
import User from 'App/Models/User'
import Role from 'Contracts/enums/Role'

export default class UserSeeder extends BaseSeeder {
  static createMany: any
  public async run () {
    // Write your database queries inside the run method
   
      await User.createMany([
        {
          username: 'elisee',
          loginId:"elisee",
          email: '',
          password: '131313',
          roleId: Role.ADMIN,
          organisation: 'NDS',
        },
        {
          username: 'admin1',
          loginId:"admin",
          email: '',
          password: 'passwordAdmin',
          roleId: Role.ADMIN,
          organisation: 'NDS',
        },
        {
          username: 'tester1',
          loginId:"tester1",
          email: 'tester1@test.com',
          password: 'passwordtester',
          roleId: Role.STUDENT,
          organisation: 'SP',
        },
        {
          username: 'tester2',
          loginId:"tester2",
          email: 'tester2@test.com',
          password: 'passwordtester',
          organisation: 'SJ',
        }
      ])
    }
  
  
}
