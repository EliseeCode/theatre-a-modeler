import { DateTime } from "luxon";
import Play from "App/Models/Play";
import User from "App/Models/User";
<<<<<<< HEAD
import { BaseModel, column, belongsTo, BelongsTo,hasMany, HasMany } from "@ioc:Adonis/Lucid/Orm";
import Line from "App/Models/Line";
=======
import Line from "App/Models/Line";
import {
  BaseModel,
  column,
  belongsTo,
  BelongsTo,
  hasMany,
  HasMany,
} from "@ioc:Adonis/Lucid/Orm";
>>>>>>> ea5f83df5451049e07a5ae8331a400e97eb50ef5

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

  @belongsTo(() => Play, { localKey: "id", foreignKey: "play_id" })
  public play: BelongsTo<typeof Play>;

  @belongsTo(() => User, { localKey: "id", foreignKey: "creator_id" })
  public creator: BelongsTo<typeof User>;

  @hasMany(() => Line, { localKey: "id", foreignKey: "scene_id" })
  public lines: HasMany<typeof Line>;

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime;

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime;
}
