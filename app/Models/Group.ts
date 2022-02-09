import { DateTime } from "luxon";
import User from "App/Models/User";
import Play from "App/Models/Play";
import {
  BaseModel,
  column,
  belongsTo,
  BelongsTo,
  manyToMany,
  ManyToMany,
} from "@ioc:Adonis/Lucid/Orm";

export default class Group extends BaseModel {
  @column({ isPrimary: true })
  public id: number;

  @column()
  public name: string;

  @column()
  public description: string;

  @column()
  public status: string;

  @column()
  public langId: number;

  @column()
  public creatorId: number;
  
  @column()
  public code: string;

  @belongsTo(() => User, { localKey: "id", foreignKey: "creatorId" })
  public creator: BelongsTo<typeof User>;

  @manyToMany(() => User, {
    localKey: "id",
    relatedKey: "id",
    pivotForeignKey: "groupId",
    pivotRelatedForeignKey: "userId",
    pivotColumns: ['role_id'],
  })
  public users: ManyToMany<typeof User>;

  @manyToMany(() => Play, {
    localKey: "id",
    relatedKey: "id",
    pivotForeignKey: "groupId",
    pivotRelatedForeignKey: "playId",
  })
  public plays: ManyToMany<typeof Play>;

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime;

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime;

}
