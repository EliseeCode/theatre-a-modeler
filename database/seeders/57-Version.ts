import BaseSeeder from "@ioc:Adonis/Lucid/Seeder";
import Version from "App/Models/Version";

export default class VersionSeeder extends BaseSeeder {
  public async run() {
    Version.createMany([
      {
        name: "In a rush",
      },
      {
        name: "Angry",
      },
      {
        name: "Hesitant",
      },
    ]);
  }
}
