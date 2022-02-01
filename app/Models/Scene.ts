import { DateTime } from "luxon";
import Play from "App/Models/Play";
import User from "App/Models/User";
import Line from "App/Models/Line";
import {
  BaseModel,
  column,
  belongsTo,
  BelongsTo,
  hasMany,
  HasMany,
} from "@ioc:Adonis/Lucid/Orm";

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
  public creatorId: number;

  @column()
  public playId: number;

  @column({ meta: { type: "number" } })
  public lineId: number;

  @belongsTo(() => Play, { localKey: "id", foreignKey: "playId" })
  public play: BelongsTo<typeof Play>;

  @belongsTo(() => User, { localKey: "id", foreignKey: "creatorId" })
  public creator: BelongsTo<typeof User>;

  @hasMany(() => Line, { localKey: "id", foreignKey: "sceneId" })
  public lines: HasMany<typeof Line>;

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime;

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime;
}
