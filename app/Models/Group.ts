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
  public lang_id: number;

  @belongsTo(() => User)
  public creator_id: BelongsTo<typeof User>;

  @manyToMany(() => User)
  public users: ManyToMany<typeof User>;

  @manyToMany(() => Play)
  public plays: ManyToMany<typeof Play>;

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime;

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime;
}
