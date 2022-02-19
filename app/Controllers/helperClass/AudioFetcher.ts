import Scene from "App/Models/Scene";
import User from "App/Models/User";
import Version from "App/Models/Version";

export default class AudioVersionFetcher {
  scene: Scene;
  constructor(scene: Scene) {
    this.scene = scene; // We need the scene instance due to conflicting line verions from different scenes, we don't want to get other doublers around!
  }
  public async getDoublersAndAudioVersionsFromLineVersionOnScene(
    version: Version
  ) {
    const doublers = new Set<User>();
    const linesOnScene = await this.scene
      .related("lines")
      .query()
      .where("version_id", version.id)
      .preload("audios", (audioQuery) => {
        audioQuery.preload("creator").preload("version");
      });
    linesOnScene.map((line) => {
      line.audios.map((audio) => {
        doublers.add(audio.creator);
        const tempAudioVersions: Version[] = audio.creator?.audioVersions ?? []; // As doubler (audio.creator) & audioVersion (audio.version) doesn't have an intermediary relation, it's best to set them in the same query, else we'll need more iterations...
        tempAudioVersions.push(audio.version);
        audio.creator.audioVersions = Array.from(new Set(tempAudioVersions));
      });
    });
    version.doublers = Array.from(doublers);
    return Array.from(doublers);
  }
}
