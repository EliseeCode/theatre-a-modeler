import { DateTime } from "luxon";
import {
  BaseModel,
  BelongsTo,
  belongsTo,
  column,
  computed,
  HasMany,
  hasMany,
} from "@ioc:Adonis/Lucid/Orm";
import User from "App/Models/User";
import Audio from "./Audio";

export default class Version extends BaseModel {
  @column({ isPrimary: true })
  public id: number;

  @column()
  public name: string;

  @column()
  public creatorId: number;

  @column()
  public type: number;

  @computed()
  public doublers: User[];

  @hasMany(() => Audio)
  public audios: HasMany<typeof Audio>

  @belongsTo(() => User, { localKey: "id", foreignKey: "creatorId" })
  public creator: BelongsTo<typeof User>;

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime;

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime;
}
