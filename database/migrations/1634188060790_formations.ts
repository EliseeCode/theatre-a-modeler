import BaseSchema from "@ioc:Adonis/Lucid/Schema";

export default class Formations extends BaseSchema {
  protected tableName = "formations";

  public async up() {
    this.schema.createTable(this.tableName, (table) => {
      table.increments("id");
      table.string("title", 255);
      table.text("description", "long");
      table.string("image_url", 255).nullable();
      table.string("information_url", 255).nullable();
      table.string("inscription_url", 255).nullable();
      table.date("starting_date").nullable();
      table.date("finishing_date").nullable();
      table.string("status", 55).notNullable().defaultTo("pending");
      table.string("lang").nullable();
      table.string("modality", 55).nullable();
      table
        .integer("creator_id", 180)
        .unsigned()
        .references("users.id")
        .onDelete("CASCADE");
      table.integer("validator_id", 180).unsigned().references("users.id");
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
