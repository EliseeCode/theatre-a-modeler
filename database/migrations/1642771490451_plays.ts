import BaseSchema from "@ioc:Adonis/Lucid/Schema";
import Status from "Contracts/enums/Status";

export default class Plays extends BaseSchema {
  protected tableName = "plays";

  public async up() {
    this.schema.createTable(this.tableName, (table) => {
      table.increments("id");
      table.string("name", 255);
      table.text("description", "long").nullable();
      table.integer("status").notNullable().defaultTo(Status.HIDDEN);
      table.integer("lang_id").nullable();
      table
        .integer("creator_id", 180)
        .unsigned()
        .references("users.id")
        .onDelete("CASCADE");
      table
        .integer("image_id", 180)
        .unsigned()
        .references("images.id")
        .nullable()
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
