import BaseSchema from "@ioc:Adonis/Lucid/Schema";

export default class LinesAlter_1s extends BaseSchema {
  //   protected tableName = "lines";

  public async up() {
    //     this.schema.alterTable(this.tableName, (table) => {
    //       table.dropUnique([
    //         "creator_id", "scene_id", "character_id", "version_id"
    //       ]);


    //     });
  }

  public async down() {
    //     this.schema.alterTable(this.tableName, (table) => {
    //       // table.dropIndex(["creator_id", "scene_id", "character_id", "version_id"]);
    //       table.unique(["creator_id", "scene_id", "character_id", "version_id"]);
    //     });
    //   }
  }
}
