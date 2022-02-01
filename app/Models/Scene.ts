import { DateTime } from "luxon";
import Play from "App/Models/Play";
import User from "App/Models/User";
import { BaseModel, column, belongsTo, BelongsTo,hasMany, HasMany } from "@ioc:Adonis/Lucid/Orm";
import Line from "App/Models/Line";

export default class Scene extends BaseModel {
  @column({ isPrimary: true })
  public id: number;

  @column()
  public name: string;

  @column()
  public position: number;

  @column()
  public description: string;

  @column()
  public status: string;

  @column()
  public langId: number;

  @column()
  public playId: number;
  
  @hasMany(()=>Line)
  public lines: HasMany<typeof Line>;

  @belongsTo(() => User)
  public creator_id: BelongsTo<typeof User>;

  // @belongsTo(() => Play)
  // public play_id: BelongsTo<typeof Play>;

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime;

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime;
}
