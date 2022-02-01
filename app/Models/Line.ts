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

  @column()
  public sceneId:number;

  @column()
  public creatorId:number;

  @column()
  public characterId:number;

  @belongsTo(() => User)
  public creator: BelongsTo<typeof User>;

  @belongsTo(() => Scene)
  public scene: BelongsTo<typeof Scene>;

  @belongsTo(() => Character)
  public character: BelongsTo<typeof Character>;

  @column()
  public langId: number;

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime;

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime;
}
