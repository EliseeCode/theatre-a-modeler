import BaseSchema from "@ioc:Adonis/Lucid/Schema";

export default class AudiosAlter_1s extends BaseSchema {
  protected tableName = "audios";

  public async up() {
    this.schema.alterTable(this.tableName, (table) => {
      table.unique(["line_id", "creator_id", "version_id"]);
    });
  }

  public async down() {
    this.schema.alterTable(this.tableName, (table) => {
      table.dropUnique(["line_id", "creator_id", "version_id"]);
    });
  }
}
