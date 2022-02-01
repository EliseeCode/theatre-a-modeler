import { DateTime } from "luxon";
import User from "App/Models/User";
import Line from "App/Models/Line";
import { BaseModel, column, belongsTo, BelongsTo } from "@ioc:Adonis/Lucid/Orm";

export default class Audio extends BaseModel {
  @column({ isPrimary: true })
  public id: number;

  @column()
  public name: string;

  @column()
  public description: string;

  @column()
  public publicPath: string;

  @column()
  public relativePath: string;

  @column()
  public langId: number;

  @column()
  public creatorId: number;

  @column()
  public lineId: number;

  @belongsTo(() => User, { localKey: "id", foreignKey: "creatorId" })
  public creator: BelongsTo<typeof User>;

  @belongsTo(() => Line, { localKey: "id", foreignKey: "lineId" })
  public line: BelongsTo<typeof Line>;

  @column()
  public size: number;

  @column()
  public type: string;

  @column()
  public mimeType: string;

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime;

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime;
}
