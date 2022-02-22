import BaseSchema from "@ioc:Adonis/Lucid/Schema";

export default class Audios extends BaseSchema {
  protected tableName = "audios";

  public async up() {
    this.schema.createTable(this.tableName, (table) => {
      table.increments("id").primary();
      table.string("name", 255).nullable();
      table.string("description", 511).nullable();
      table.string("public_path", 511).notNullable();
      table.string("relative_path", 511).notNullable();
      table
        .integer("creator_id", 180)
        .unsigned()
        .references("users.id")
        .onDelete("CASCADE")
        .notNullable();
      table
        .integer("line_id", 180)
        .unsigned()
        .references("lines.id")
        .onDelete("CASCADE")
        .notNullable();
      table.bigInteger("size").notNullable(); // #FIXME in MB/KB
      table.string("type", 255).notNullable();
      table.string("mime_type", 255).notNullable();
      table.integer("lang_id", 180).nullable();
      table
        .integer("version_id", 180)
        .unsigned()
        .references("versions.id")
        .onDelete("CASCADE")
        .notNullable();

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
