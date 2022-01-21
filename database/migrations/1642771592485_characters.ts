import BaseSchema from '@ioc:Adonis/Lucid/Schema'

export default class Characters extends BaseSchema {
  protected tableName = 'characters'

  public async up () {
    this.schema.createTable(this.tableName, (table) => {
      table.increments('id')
      table.string('name',255).nullable()
      table.enum("gender",["male","female","other"]).nullable();
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
