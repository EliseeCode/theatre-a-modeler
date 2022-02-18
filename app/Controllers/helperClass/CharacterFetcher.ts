import Play from "App/Models/Play";
import Scene from "App/Models/Scene";
import Character from "App/Models/Character";

export default class CharacterFetcher {

    public async getCharactersFromPlay(play: Play) {
        var charactersSet = new Set<number>();
        await play.load("scenes", (scenesQuery) => {
            scenesQuery.preload("lines");
        });
        play.scenes.forEach((scene) => {
            scene.lines.forEach((line) => {
                charactersSet.add(+line.characterId);
            });
        });

        let charactersArray: number[] = Array.from(charactersSet);
        const res = await Character.query().preload('image').whereIn('id', charactersArray);

        play.characters = res;
        return res;
    }

    public async getCharactersFromScene(scene: Scene) {
        var charactersSet = new Set<number>();
        await scene.load("lines");
        scene.lines.forEach((el) => {
            charactersSet.add(el.characterId);
        });
        const charactersArray: number[] = Array.from(charactersSet);
        const res = await Character.query().preload('image').whereIn('id', charactersArray);
        scene.characters = res;
        return res;
    }
}