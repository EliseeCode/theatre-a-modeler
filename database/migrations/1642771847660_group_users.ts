import BaseSchema from '@ioc:Adonis/Lucid/Schema'

export default class GroupUsers extends BaseSchema {
  protected tableName = 'group_user'

  public async up () {
    this.schema.createTable(this.tableName, (table) => {
      table.increments('id')
      table.integer("position")
      table.integer("user_id", 180)
      .unsigned()
      .references('users.id')
      .onDelete('CASCADE');
      table.integer("group_id", 180)
      .unsigned()
      .references('groups.id')
      .onDelete('CASCADE');
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
