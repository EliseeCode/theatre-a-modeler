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
  HasMany,
} from "@ioc:Adonis/Lucid/Orm";
import Scene from "./Scene";

export default class Play extends BaseModel {
  @column({ isPrimary: true })
  public id: number;

  @column({ meta: { type: "string" } })
  public name: string;

  @column({ meta: { type: "string" } })
  public description: string;

  @column({ meta: { type: "string" } })
  public status: string;

  @column({ meta: { type: "number" } })
  public langId: number;
<<<<<<< HEAD

  @column({ columnName: 'creator_id'})
  public userId: number;

  @belongsTo(() => User)
=======

  @column({ meta: { type: "number" } })
  public creatorId: number;

  @column({ meta: { type: "number" } })
  public sceneId: number;

  @belongsTo(() => User, { localKey: "id", foreignKey: "creator_id" })
>>>>>>> ea5f83df5451049e07a5ae8331a400e97eb50ef5
  public creator: BelongsTo<typeof User>;

  @manyToMany(() => Group, {
    localKey: "id",
    relatedKey: "id",
    pivotForeignKey: "play_id",
    pivotRelatedForeignKey: "group_id",
  })
  public groups: ManyToMany<typeof Group>;

  @hasMany(() => Scene, { localKey: "id", foreignKey: "play_id" })
  public scenes: HasMany<typeof Scene>;

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime;

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime;
}
