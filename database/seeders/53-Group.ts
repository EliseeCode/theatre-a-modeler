import BaseSeeder from "@ioc:Adonis/Lucid/Seeder";
import Group from "App/Models/Group";

export default class GroupSeeder extends BaseSeeder {
  public async run() {
    // Write your database queries inside the run method
    await Group.createMany([
      {
        name: "HAZ-A",
        creatorId: 1,
        code: "ABCDED",
      },
      {
        name: "HAZ-B",
        creatorId: 1,
        code: "azerty",
      },
      {
        name: "HAZ-C",
        creatorId: 1,
        code: "azertyu",
      },
    ]);
  }
}
