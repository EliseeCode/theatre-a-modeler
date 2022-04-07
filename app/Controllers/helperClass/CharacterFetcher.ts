import Play from "App/Models/Play";
import Scene from "App/Models/Scene";
import Character from "App/Models/Character";
import Database from "@ioc:Adonis/Lucid/Database";

export default class CharacterFetcher {

    public async getCharactersFromPlay(play: Play) {
        const charactersTable = await Database.query()
            .select('lines.character_id')
            .from('lines')
            .join('scenes', 'lines.scene_id', 'scenes.id')
            .where('scenes.play_id', play.id)
            .distinct('character_id');

        let charactersArray = charactersTable.map((el) => el.character_id)
        const res = await Character.query().preload('image').whereIn('id', charactersArray);
        play.characters = res;
        return res;
    }

    public async getCharactersFromScene(scene: Scene) {

        const charactersTable = await Database.query()
            .select('character_id')
            .from('character_scene')
            .where('scene_id', scene.id);

        let charactersArray = charactersTable.map((el) => el.character_id)

        const res = await Character.query().whereIn('id', charactersArray).preload('image');

        return res;
    }

}