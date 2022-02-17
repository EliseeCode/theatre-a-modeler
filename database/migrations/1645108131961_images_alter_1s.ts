import BaseSchema from '@ioc:Adonis/Lucid/Schema'

export default class GroupAddColors extends BaseSchema {
  protected tableName = 'images'

  public async up() {
    this.schema.alterTable(this.tableName, (table) => {
      table.dropColumn("description")
    })
  }

  public async down() {
    this.schema.alterTable(this.tableName, (table) => {
      table.string("description", 255)
    })
  }
}
