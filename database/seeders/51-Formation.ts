import BaseSeeder from '@ioc:Adonis/Lucid/Seeder'
import Formation from 'App/Models/Formation'


export default class FormationSeeder extends BaseSeeder {
  public async run () {
    // Write your database queries inside the run method
    await Formation.createMany([
      {
        title: 'PNI_FR',
        description: 'Plusieurs ateliers et formations en ligne à SB',
        imageUrl: null,
        informationUrl: 'www.sb.k12.tr',
        startingDate: new Date("2022-02-25"),
        finishingDate: new Date("2022-02-25"),
        status: 'pending',
        langId: 1,
        modality: 'online',
        creatorId: 2,
        validatorId: null,
      },
      {
        title: 'PNI_TR',
        description: 'Plusieurs ateliers et formations en ligne à SB',
        imageUrl: null,
        informationUrl: 'www.sb.k12.tr',
        startingDate: new Date("2022-02-25"),
        finishingDate: new Date("2022-02-25"),
        status: 'pending',
        langId: 2,
        modality: 'online',
        creatorId: 2,
        validatorId: null,
      }
    ])
  }
}
