import { DateTime } from "luxon";
import Scene from "App/Models/Scene";
import Character from "App/Models/Character";
import User from "App/Models/User";
import { BaseModel, column, belongsTo, BelongsTo } from "@ioc:Adonis/Lucid/Orm";

export default class Line extends BaseModel {
  @column({ isPrimary: true })
  public id: number;

  @column()
  public text: string;

  @column()
  public position: number;

  @column()
  public status: string;

  @belongsTo(() => User)
  public creator_id: BelongsTo<typeof User>;

  @belongsTo(() => Scene)
  public scene_id: BelongsTo<typeof Scene>;

  @belongsTo(() => Character)
  public character_id: BelongsTo<typeof Character>;

  @column()
  public lang_id: number;

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime;

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime;
}
