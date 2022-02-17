import { DateTime } from "luxon";
import User from "App/Models/User";
import Line from "App/Models/Line";
import { BaseModel, column, belongsTo, BelongsTo } from "@ioc:Adonis/Lucid/Orm";
import Version from "App/Models/Version";

export default class Audios extends BaseModel {
  @column({ isPrimary: true, meta: { type: "number" } })
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
  public langId: number;

  @column({ meta: { type: "number" } })
  public creatorId: number;

  @column({ meta: { type: "number" } })
  public lineId: number;

  @column({ meta: { type: "number" } })
  public versionId: number;

  @belongsTo(() => User, { localKey: "id", foreignKey: "creatorId" })
  public creator: BelongsTo<typeof User>;

  @belongsTo(() => Line, { localKey: "id", foreignKey: "lineId" })
  public line: BelongsTo<typeof Line>;

  @belongsTo(() => Version, { localKey: "id", foreignKey: "versionId" })
  public version: BelongsTo<typeof Version>;

  @column({ meta: { type: "number" } })
  public size: number;

  @column({ meta: { type: "string" } })
  public type: string;

  @column({ meta: { type: "string" } })
  public mimeType: string;

  @column.dateTime({ autoCreate: true, meta: { type: "datetime" } })
  public createdAt: DateTime;

  @column.dateTime({
    autoCreate: true,
    autoUpdate: true,
    meta: { type: "datetime" },
  })
  public updatedAt: DateTime;
}
