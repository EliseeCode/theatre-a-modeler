import BaseSchema from '@ioc:Adonis/Lucid/Schema'

export default class AlterImages extends BaseSchema {
  protected tableName = 'images'

  public async up() {
    this.schema.alterTable(this.tableName, (table) => {
      table.string("status", 255).defaultTo(null)
    })
  }

  public async down() {
    this.schema.alterTable(this.tableName, (table) => {
      table.dropColumn("status")
    })
  }
}
