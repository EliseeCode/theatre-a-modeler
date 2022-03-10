import BaseSchema from '@ioc:Adonis/Lucid/Schema'

export default class CharacterScenes extends BaseSchema {
  protected tableName = 'character_scene'

  public async up() {
    this.schema.createTable(this.tableName, (table) => {
      table.increments('id')
      table.integer("character_id", 180)
        .unsigned()
        .references('characters.id')
        .onDelete('CASCADE');
      table.integer("scene_id", 180)
        .unsigned()
        .references('scenes.id')
        .onDelete('CASCADE');
      table.unique(['character_id', 'scene_id'])
      /**
       * Uses timestamptz for PostgreSQL and DATETIME2 for MSSQL
       */
      table.timestamp('created_at', { useTz: true })
      table.timestamp('updated_at', { useTz: true })
    })
  }

  public async down() {
    this.schema.dropTable(this.tableName)
  }
}
