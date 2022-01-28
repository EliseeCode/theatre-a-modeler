import { DateTime } from "luxon";
import Hash from "@ioc:Adonis/Core/Hash";
import {
  column,
  beforeSave,
  BaseModel,
  manyToMany,
  ManyToMany,
  /*hasMany,
  HasMany,*/
} from "@ioc:Adonis/Lucid/Orm";
//import Formation from "./Formation";
import Group from "App/Models/Group";
export default class User extends BaseModel {
  protected tableName = "users";
  @column({ isPrimary: true })
  public id: number;

  @column()
  public username: string;

  @column()
  public loginId: string;

  @column()
  public email: string;

  @column()
  public role: string;

  @column()
  public organisation: string;

  @column({ serializeAs: null })
  public password: string;

  @column()
  public rememberMeToken?: string;

  @manyToMany(() => Group)
  public groups: ManyToMany<typeof Group>;

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
