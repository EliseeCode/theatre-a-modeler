import { DateTime } from 'luxon'
import { BaseModel, column } from '@ioc:Adonis/Lucid/Orm'

export default class Play extends BaseModel {
  @column({ isPrimary: true })
  public id: number

  @column({meta:{type:'string'}})
  public name: string

  @column({meta:{type:'string'}})
  public description: string

  @column({meta:{type:'string'}})
  public status: string

  @column({meta:{type:'number'}})
  public lang_id: number

  @column({meta:{type:'number'}})
  public creator_id: number

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime
}
