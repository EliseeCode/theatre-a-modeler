import BaseSchema from "@ioc:Adonis/Lucid/Schema";

export default class Versions extends BaseSchema {
  protected tableName = "versions";

  public async up() {
    this.schema.createTable(this.tableName, (table) => {
      table.increments("id").primary();
      table.string("name", 255).notNullable().defaultTo("official");
      table.integer("creator_id", 180).unsigned().references("users.id");
      table.integer("type").nullable;
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
