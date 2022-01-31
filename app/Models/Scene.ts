import { DateTime } from "luxon";
import Play from "App/Models/Play";
import User from "App/Models/User";
import { BaseModel, column, belongsTo, BelongsTo } from "@ioc:Adonis/Lucid/Orm";

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
  public creator_id: number;

  @column()
  public playId: number;

  @belongsTo(() => Play, { localKey: "id", foreignKey: "play_id" })
  public play: BelongsTo<typeof Play>;

  @belongsTo(() => User, { localKey: "id", foreignKey: "creator_id" })
  public creator: BelongsTo<typeof User>;

  // @belongsTo(() => Play)
  // public playId: BelongsTo<typeof Play>;

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime;

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime;
}
