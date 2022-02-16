import BaseSchema from "@ioc:Adonis/Lucid/Schema";

export default class CharacterPlays extends BaseSchema {
  protected tableName = "character_play";

  public async up() {
    this.schema.createTable(this.tableName, (table) => {
      table.increments("id");
      table
        .integer("play_id", 180)
        .unsigned()
        .references("plays.id")
        .onDelete("CASCADE");
      table
        .integer("character_id", 180)
        .unsigned()
        .references("characters.id")
        .onDelete("CASCADE");
      table.unique(["play_id", "character_id"]);

      /**
       * Uses timestamptz for PostgreSQL and DATETIME2 for MSSQL
       */
      table.timestamp("created_at", { useTz: true });
      table.timestamp("updated_at", { useTz: true });
    });
  }

  public async down() {
    this.schema.dropTable(this.tableName);
  }
}
