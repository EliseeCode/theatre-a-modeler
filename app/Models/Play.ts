import { DateTime } from "luxon";
import User from "App/Models/User";
import Group from "App/Models/Group";
import {
  BaseModel,
  column,
  belongsTo,
  BelongsTo,
  manyToMany,
  ManyToMany,
  hasMany,
  HasMany
} from "@ioc:Adonis/Lucid/Orm";
import Scene from "App/Models/Scene";
import Image from "App/Models/Image";
import Character from "App/Models/Character";

export default class Play extends BaseModel {
  @column({ isPrimary: true })
  public id: number;

  @column({ meta: { type: "string" } })
  public name: string;

  @column({ meta: { type: "string" } })
  public description: string;

  @column()
  public status: number;

  @column({ meta: { type: "number" } })
  public langId: number;

  @column()
  public imageId: number;

  @belongsTo(() => Image)
  public image: BelongsTo<typeof Image>;

  @column()
  public creatorId: number;

  @column({ meta: { type: "number" } })
  public sceneId: number;

  @belongsTo(() => User, { localKey: "id", foreignKey: "creatorId" })
  public creator: BelongsTo<typeof User>;

  @manyToMany(() => Group, {
    localKey: "id",
    relatedKey: "id",
    pivotForeignKey: "play_id",
    pivotRelatedForeignKey: "group_id",
  })
  public groups: ManyToMany<typeof Group>;

  @hasMany(() => Scene, { localKey: "id", foreignKey: "playId" })
  public scenes: HasMany<typeof Scene>;

  public characters: Character[];

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime;

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime;
}
