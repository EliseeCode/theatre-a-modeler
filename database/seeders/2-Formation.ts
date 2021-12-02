import BaseSeeder from '@ioc:Adonis/Lucid/Seeder'
import Formation from 'App/Models/Formation'


export default class FormationSeeder extends BaseSeeder {
  public async run () {
    // Write your database queries inside the run method
    await Formation.createMany([
      {
        title: 'PNI_FR',
        description: 'Plusieurs ateliers et formations en ligne à SB',
        image_url: null,
        information_url: 'www.sb.k12.tr',
        starting_date: new Date("2022-02-25"),
        finishing_date: new Date("2022-02-25"),
        status: 'pending',
        lang: 'fr',
        modality: 'online',
        creator_id: 2,
        validator_id: null,
      },
      {
        title: 'PNI_TR',
        description: 'Plusieurs ateliers et formations en ligne à SB',
        image_url: null,
        information_url: 'www.sb.k12.tr',
        starting_date: new Date("2022-02-25"),
        finishing_date: new Date("2022-02-25"),
        status: 'pending',
        lang: 'tr',
        modality: 'online',
        creator_id: 2,
        validator_id: null,
      }
    ])
  }
}
