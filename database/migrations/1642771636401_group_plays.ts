import BaseSchema from "@ioc:Adonis/Lucid/Schema";

export default class GroupPlays extends BaseSchema {
  protected tableName = "group_play";

  public async up() {
    this.schema.createTable(this.tableName, (table) => {
      table.increments("id");
      table.integer("position");
      table
        .integer("play_id", 180)
        .unsigned()
        .references("plays.id")
        .onDelete("CASCADE");
      table
        .integer("group_id", 180)
        .unsigned()
        .references("groups.id")
        .onDelete("CASCADE");
      table.unique(["play_id", "group_id"]);
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
