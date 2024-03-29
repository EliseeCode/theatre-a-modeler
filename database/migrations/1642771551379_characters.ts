import BaseSchema from "@ioc:Adonis/Lucid/Schema";

export default class Characters extends BaseSchema {
  protected tableName = "characters";

  public async up() {
    this.schema.createTable(this.tableName, (table) => {
      table.increments("id");
      table.string("name", 255).nullable();
      table.string("gender").nullable();
      table
        .integer("image_id", 180)
        .unsigned()
        .references("images.id")
        .onDelete("SET NULL");
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
