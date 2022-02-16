import BaseSchema from '@ioc:Adonis/Lucid/Schema'

export default class GroupAddColors extends BaseSchema {
  protected tableName = 'groups'

  public async up() {
    this.schema.alterTable(this.tableName, (table) => {
      table.string("color", 7)
    })
  }

  public async down() {
    this.schema.alterTable(this.tableName, (table) => {
      table.dropColumn("color")
    })
  }
}
