import { DateTime } from "luxon";
import { BaseModel, column, BelongsTo, belongsTo } from "@ioc:Adonis/Lucid/Orm";
import User from "./User";
export default class Formation extends BaseModel {
  @column({ isPrimary: true })
  public id: number;

  @column()
  public title: string;

  @column()
  public description: string;

  @column()
  public imageUrl: string | null;

  @column()
  public informationUrl: string | null;

  @column()
  public inscriptionUrl: string | null;

  @column()
  public startingDate: Date | null;

  @column()
  public finishingDate: Date | null;

  @column()
  public status: string;

  @column()
  public lang: string;

  @column()
  public modality: string | null;

  @column()
  public creatorId: number;

  @column()
  public validatorId: number | null;

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime;

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime;

  @belongsTo(() => User, { localKey: "id", foreignKey: "creatorId" })
  public creator: BelongsTo<typeof User>;
}
