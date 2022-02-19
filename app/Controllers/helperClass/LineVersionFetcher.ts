import Character from "App/Models/Character";
import Scene from "App/Models/Scene";
import Version from "App/Models/Version";
export default class LineVersionFetcher {
  scene: Scene;
  constructor(scene: Scene) {
    this.scene = scene; // We need the scene instance due to conflicting line verions from different scenes, we don't want to get other doublers around!
  }
  public async getVersionsFromCharacterOnScene(character: Character) {
    const lineVersions = new Set<Version>();
    const linesOnScene = await this.scene
      .related("lines")
      .query()
      .where("character_id", character.id)
      .preload("version");
    linesOnScene.map((line) => {
      // console.log(line.position, line.text.slice(0, 10), line.version.id);
      lineVersions.add(line.version);
    });
    character.versions = Array.from(lineVersions);
    return Array.from(lineVersions);
  }
}
