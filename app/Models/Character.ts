import { DateTime } from "luxon";
import {
  BaseModel,
  BelongsTo,
  belongsTo,
  column,
  HasMany,
  hasMany,
  ManyToMany,
  manyToMany,
} from "@ioc:Adonis/Lucid/Orm";
import Play from "App/Models/Play";
import Image from "./Image";
import Line from "./Line";

export default class Character extends BaseModel {
  @column({ isPrimary: true })
  public id: number;

  @column({ meta: { type: "string" } })
  public name: string;

  @column()
  public description: string;

  @column({ meta: { type: "string" } })
  public gender: string;

  @column({ meta: { type: "number" } })
  public imageId: number;

  @hasMany(() => Line, {})
  public lines: HasMany<typeof Line>
  // @manyToMany(() => Play, {
  //   localKey: "id",
  //   relatedKey: "id",
  //   pivotForeignKey: "character_id",
  //   pivotRelatedForeignKey: "play_id",
  // })
  // public plays: ManyToMany<typeof Play>;

  @belongsTo(() => Image, { localKey: "id", foreignKey: "imageId" })
  public image: BelongsTo<typeof Image>;

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime;

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime;
}
