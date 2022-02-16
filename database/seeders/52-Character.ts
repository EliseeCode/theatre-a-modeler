import BaseSeeder from "@ioc:Adonis/Lucid/Seeder";
import Character from "App/Models/Character";

export default class CharacterSeeder extends BaseSeeder {
  public async run() {
    // Write your database queries inside the run method
    await Character.createMany([
      {
        name: "Albert",
        gender: "male",
      },
      {
        name: "Betty",
        gender: "female",
      },
      {
        name: "Charlie",
        gender: "male",
      },
    ]);
  }
}
