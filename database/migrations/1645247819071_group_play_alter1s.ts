import BaseSchema from '@ioc:Adonis/Lucid/Schema'
import Status from 'Contracts/enums/Status'

export default class GroupPlayAlter1s extends BaseSchema {
  protected tableName = 'group_play'

  public async up() {
    this.schema.alterTable(this.tableName, (table) => {
      table.integer("status", 255).defaultTo(Status.PUBLIC)
    })
  }

  public async down() {
    this.schema.alterTable(this.tableName, (table) => {
      table.dropColumn("status")
    })
  }
}
