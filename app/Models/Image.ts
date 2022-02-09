import { DateTime } from "luxon";
import { BaseModel, BelongsTo, belongsTo, column } from "@ioc:Adonis/Lucid/Orm";
import User from "App/Models/User";

export default class Image extends BaseModel {
  @column({ isPrimary: true })
  public id: number;

  @column({ meta: { type: "string" } })
  public name: string;

  @column({ meta: { type: "string" } })
  public description: string;

  @column({ meta: { type: "string" } })
  public publicPath: string;

  @column({ meta: { type: "string" } })
  public relativePath: string;

  @column({ meta: { type: "number" } })
  public creatorId: number;

  @column({ meta: { type: "number" } })
  public size: number;

  @column({ meta: { type: "string" } })
  public type: string;

  @column({ meta: { type: "string" } })
  public mimeType: string;

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime;

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime;
}
