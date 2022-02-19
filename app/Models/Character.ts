import { DateTime } from "luxon";
import {
  BaseModel,
  BelongsTo,
  belongsTo,
  column,
  computed
} from "@ioc:Adonis/Lucid/Orm";
import Image from "App/Models/Image";
import Version from "App/Models/Version";

export default class Character extends BaseModel {
  @column({ isPrimary: true })
  public id: number;

  @column({ meta: { type: "string" } })
  public name: string;

  @column()
  public description: string;

  @column({ meta: { type: "string" } })
  public gender: string;

  @column({ meta: { type: "number" }, serializeAs: null })
  public imageId: number;

  @computed()
  public versions: Version[];

  @belongsTo(() => Image, {
    localKey: "id",
    foreignKey: "imageId",
    serializeAs: "image"
  })
  public image: BelongsTo<typeof Image>;

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime;

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime;
}
