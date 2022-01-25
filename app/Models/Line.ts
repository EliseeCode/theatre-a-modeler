import { DateTime } from 'luxon'
import { BaseModel, column } from '@ioc:Adonis/Lucid/Orm'

export default class Line extends BaseModel {
  @column({ isPrimary: true })
  public id: number
  
  @column()
  public text: string
  
  @column()
  public position: number

  @column()
  public status: string
  
  @column()
  public creator_id: number
  
  @column()
  public scene_id: number

  @column()
  public character_id: number

  @column()
  public lang_id: number
      
  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime
}
