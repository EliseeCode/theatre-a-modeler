import { DateTime } from "luxon";
import Scene from "App/Models/Scene";
import Character from "App/Models/Character";
import User from "App/Models/User";
import Audio from "App/Models/Audio";
import {
  BaseModel,
  column,
  belongsTo,
  BelongsTo,
  hasMany,
  HasMany,
  computed,
} from "@ioc:Adonis/Lucid/Orm";
import Version from "App/Models/Version";

export default class Line extends BaseModel {
  @column({ isPrimary: true })
  public id: number;

  @computed()
  public isAlternative: boolean;

  @computed()
  public toBeRecorded: boolean;

  @computed()
  public isRobotized: boolean;

  @column()
  public text: string;

  @column()
  public position: number;

  @column()
  public status: string;

  @column()
  public creatorId: number;

  @column()
  public sceneId: number;

  @column()
  public characterId: number;

  @belongsTo(() => User, { localKey: "id", foreignKey: "creatorId" })
  public creator: BelongsTo<typeof User>;

  @belongsTo(() => Scene, { localKey: "id", foreignKey: "sceneId" })
  public scene: BelongsTo<typeof Scene>;

  @belongsTo(() => Character, { localKey: "id", foreignKey: "characterId" })
  public character: BelongsTo<typeof Character>;

  @hasMany(() => Audio, { localKey: "id", foreignKey: "lineId" })
  public audios: HasMany<typeof Audio>;

  @column()
  public langId: number;

  @column()
  public versionId: number;

  @belongsTo(() => Version, { localKey: "id", foreignKey: "versionId" })
  public version: BelongsTo<typeof Version>;

  @column.dateTime({ autoCreate: true })
  public createdAt: DateTime;

  @column.dateTime({ autoCreate: true, autoUpdate: true })
  public updatedAt: DateTime;
}
