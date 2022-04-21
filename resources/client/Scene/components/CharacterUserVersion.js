
import React, { useState, useRef, useEffect } from 'react'
import { connect } from "react-redux"
import { selectCharacterAudioVersion } from "../actions/charactersAction"
import Speech from 'speak-tts'

const CharacterUserVersion = (props) => {
    const { characters, characterId, audios, lines, versions, userId } = props;
    const speech = new Speech();
    const robotIsSupported = speech.hasBrowserSupport();
    const character = characters.byIds[characterId];

    var data = { versions: {} };
    for (let k in lines.ids) {
        let lineId = lines.ids[k];
        let line = lines.byIds[lineId];
        if (line.character_id == characterId) {
            for (let audioId in audios.byIds) {
                let audio = audios.byIds[audioId]
                if (audio.line_id == lineId) {
                    data = {
                        ...data,
                        versions: {
                            ...data?.versions,
                            [audio?.version_id]: {
                                ...data?.versions[audio?.version_id],
                                id: [audio?.version_id],
                                name: versions.byIds[audio?.version_id].name,
                                audios: data?.versions[audio.version_id]?.audios ? data?.versions[audio.version_id]?.audios + 1 : 1
                            }
                        }
                    }
                }
            }
        }
    }
    const [selectedVersion, setSelectedVersion] = useState(character?.selectedAudioVersion);
    useEffect(() => {
        setSelectedVersion(character?.selectedAudioVersion);
    }, [characters])

    function handleVersionChange(e) {
        var audioVersionId = e.target.value;
        props.selectCharacterAudioVersion(characterId, audioVersionId);
        setSelectedVersion(audioVersionId);
    }
    return (<div className="level">{character.name}
        <div className="select">
            <select value={selectedVersion} onChange={(e) => { handleVersionChange(e) }}>
                {userId != "undefined" && <option value={-1}>Enregistrer</option>}
                <option value={-2}>Voix robotisé</option>
                {Object.values(data.versions).map((v) => { return (<option key={v.id} value={v.id}>{`${v.name} (${v.audios} audios)`}</option>) })}
            </select>
        </div>
    </div>)
}

const mapStateToProps = (state) => {
    return {
        sceneId: state.scenes.selectedId,
        lines: state.lines,
        characters: state.characters,
        versions: state.versions,
        userId: state.miscellaneous.user.userId
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        selectCharacterAudioVersion: (characterId, audioVersionId) => {
            dispatch(selectCharacterAudioVersion(characterId, audioVersionId));
        }
    };
};



export default connect(mapStateToProps, mapDispatchToProps)(CharacterUserVersion);
