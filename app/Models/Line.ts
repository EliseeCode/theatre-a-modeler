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
  public creatorId: number;

  @column()
  public sceneId: number;

  @column()
  public characterId: number;

  @belongsTo(() => User, { localKey: "id", foreignKey: "creator_id" })
  public creator: BelongsTo<typeof User>;

  @belongsTo(() => Scene, { localKey: "id", foreignKey: "scene_id" })
  public scene: BelongsTo<typeof Scene>;

  @belongsTo(() => Character, { localKey: "id", foreignKey: "character_id" })
  public character: BelongsTo<typeof Character>;

  @column()
  public langId: number;

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime;

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime;
}
