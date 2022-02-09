import { DateTime } from "luxon";
import Hash from "@ioc:Adonis/Core/Hash";
import {
  column,
  beforeSave,
  BaseModel,
  manyToMany,
  ManyToMany,
  hasMany,
  HasMany
} from "@ioc:Adonis/Lucid/Orm";
//import Formation from "./Formation";
import Group from "App/Models/Group";
import Play from "App/Models/Play";
export default class User extends BaseModel {
  @column({ isPrimary: true })
  public id: number;

  @column()
  public username: string;

  @column()
  public loginId: string;

  @column()
  public email: string;

  @column()
  public roleId: number;

  @column()
  public organisation: string;

  @column({ serializeAs: null })
  public password: string;

  @column()
  public rememberMeToken?: string;

  @manyToMany(() => Group, {pivotColumns: ['role_id'],})
  public groups: ManyToMany<typeof Group>;

  
  @hasMany(() => Play, {
    localKey: "id",
    foreignKey: "creatorId",
  })
  public plays: HasMany<typeof Play>;

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime;

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime;

  /*@hasMany(() => Formation,{foreignKey:"creator_id"})
  public formations: HasMany<typeof Formation>;*/

  @beforeSave()
  public static async hashPassword(user: User) {
    if (user.$dirty.password) {
      user.password = await Hash.make(user.password);
    }
  }
}
