import { DateTime } from 'luxon'
import { BaseModel, column, BelongsTo, belongsTo} from '@ioc:Adonis/Lucid/Orm'
import User from './User'
export default class Formation extends BaseModel {
  @column({ isPrimary: true })
  public id: number
  
  @column()
  public title: string

  @column()
  public description: string

  @column()
  public image_url: string | null

  @column()
  public information_url: string | null

  @column()
  public inscription_url: string | null

  @column()
  public starting_date: Date | null

  @column()
  public finishing_date: Date | null

  @column()
  public status: string

  @column()
  public lang: string

  @column()
  public modality: string | null

  @column()
  public creatorId: number

  @column()
  public validatorId: number | null

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime

  @belongsTo(() => User,{localKey:'id',foreignKey: 'creatorId',})
  public creator: BelongsTo<typeof User>;

  @belongsTo(() => User,{localKey:'id',foreignKey: 'validatorId',})
  public validator: BelongsTo<typeof User>;
}
