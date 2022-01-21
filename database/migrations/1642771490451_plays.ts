import BaseSchema from '@ioc:Adonis/Lucid/Schema'

export default class Plays extends BaseSchema {
  protected tableName = 'plays'

  public async up () {
    this.schema.createTable(this.tableName, (table) => {
      table.increments('id')
      table.string('name',255)
      table.text('description','long').nullable()
      table.string("status",55).notNullable().defaultTo("active")
      table.integer("lang_id").nullable()
      table.integer("creator_id", 180)
      .unsigned()
      .references('users.id')
      /**
       * Uses timestamptz for PostgreSQL and DATETIME2 for MSSQL
       */
      table.timestamp('created_at', { useTz: true })
      table.timestamp('updated_at', { useTz: true })
    })
  }

  public async down () {
    this.schema.dropTable(this.tableName)
  }
}
