import BaseSchema from '@ioc:Adonis/Lucid/Schema'

export default class CharacterAlter_1s extends BaseSchema {
  protected tableName = 'characters'

  public async up() {
    this.schema.alterTable(this.tableName, (table) => {
      table.string("description", 255)
    })
  }

  public async down() {
    this.schema.alterTable(this.tableName, (table) => {
      table.dropColumn("description")
    })
  }
}
