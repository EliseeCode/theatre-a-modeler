import BaseSchema from "@ioc:Adonis/Lucid/Schema";

export default class Lines extends BaseSchema {
  protected tableName = "lines";

  public async up() {
    this.schema.createTable(this.tableName, (table) => {
      table.increments("id");
      table.text("text", "long").notNullable().defaultTo("");
      table.integer("position");
      table.string("status", 55).notNullable().defaultTo("active");
      table.integer("lang_id").nullable();
      table.integer("creator_id", 180).unsigned().references("users.id");
      table
        .integer("scene_id", 180)
        .unsigned()
        .references("scenes.id")
        .onDelete("CASCADE");
      table
        .integer("character_id", 180)
        .unsigned()
        .references("characters.id")
        .onDelete("CASCADE");
      table
        .integer("version_id", 180)
        .unsigned()
        .references("versions.id")
        .onDelete("CASCADE")
        .nullable();
      /**
       * Uses timestamptz for PostgreSQL and DATETIME2 for MSSQL
       */
      table.unique(["creator_id", "scene_id", "character_id", "version_id"]);
      table.timestamp("created_at", { useTz: true });
      table.timestamp("updated_at", { useTz: true });
    });
  }

  public async down() {
    this.schema.dropTable(this.tableName);
  }
}
