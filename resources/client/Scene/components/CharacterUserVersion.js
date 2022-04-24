
import React, { useState, useEffect } from 'react'
import { connect } from "react-redux"
import { removeCharacterAudioVersion, removeCharacterTextVersion, selectCharacterAudioVersion, selectCharacterTextVersion, createCharacterTextVersion } from "../actions/charactersAction"
import Speech from 'speak-tts'

const CharacterUserVersion = (props) => {
    const { sceneId, textVersions, characters, characterId, audios, lines, audioVersions, userId } = props;
    const speech = new Speech();
    const robotIsSupported = speech.hasBrowserSupport();
    const [character, setCharacter] = useState(characters.byIds[characterId]);

    //Création d'un object pour avoir data.audioVersion{versionId:{id,name,audios(nbre d'audio)}}
    const [data, setData] = useState({ audioVersions: {}, textVersions: {} });
    useEffect(() => {
        let dataTmp = { audioVersions: {}, textVersions: {} };
        for (let k in textVersions.ids) {
            let textVersionId = textVersions.ids[k];
            let textVersion = textVersions.byIds[textVersionId];
            if (textVersion.character_id == characterId) {
                //textVersion
                dataTmp = {
                    ...dataTmp,
                    textVersions: {
                        ...dataTmp.textVersions,
                        [textVersionId]: { ...textVersion }
                    }
                }
            }
        }
        for (let k in lines.ids) {
            let lineId = lines.ids[k];
            let line = lines.byIds[lineId];
            //AudioVersion
            if (line.character_id == characterId) {
                for (let audioId in audios.byIds) {
                    let audio = audios.byIds[audioId]
                    if (audio.line_id == lineId) {
                        if (audioVersions.ids.includes(audio?.version_id)) {
                            dataTmp = {
                                ...dataTmp,
                                audioVersions: {
                                    ...dataTmp?.audioVersions,
                                    [audio?.version_id]: {
                                        ...dataTmp?.audioVersions[audio?.version_id],
                                        id: [audio?.version_id],
                                        name: audioVersions.byIds[audio?.version_id].name,
                                        audios: dataTmp?.audioVersions[audio.version_id]?.audios ? dataTmp?.audioVersions[audio.version_id]?.audios + 1 : 1
                                    }
                                }
                            }
                        }
                    }
                }
            }

        }

        setData(dataTmp);
    }, [textVersions, characters, audioVersions, audios, lines])


    const [selectedAudioVersion, setSelectedAudioVersion] = useState(character?.selectedAudioVersion);
    const [selectedTextVersion, setSelectedTextVersion] = useState(character?.selectedTextVersion || 1);

    useEffect(() => {
        setSelectedAudioVersion(character?.selectedAudioVersion);
        setSelectedTextVersion(character?.selectedTextVersion || 1);
    }, [character])

    useEffect(() => {
        setCharacter(characters.byIds[characterId]);
    }, [characters, characterId])
    function handleAudioVersionChange(e) {
        var audioVersionId = e.target.value;
        props.selectCharacterAudioVersion(characterId, audioVersionId);
        setSelectedAudioVersion(audioVersionId);
    }
    function handleTextVersionChange(e) {
        var textVersionId = e.target.value;
        if (textVersionId == -1) {
            props.createCharacterTextVersion(characterId, sceneId);
        }
        else {
            props.selectCharacterTextVersion(characterId, textVersionId);
            setSelectedTextVersion(textVersionId);
        }
    }
    function removeCharacterTextVersion() {
        props.removeCharacterTextVersion(characterId, selectedTextVersion);
    }
    function removeCharacterAudioVersion() {
        props.removeCharacterAudioVersion(characterId, selectedAudioVersion);
    }
    const characterSmallImageStyle = { width: '40px', height: '40px', objectFit: 'contain' };

    return (<>
        {/* <pre>{JSON.stringify(Object.keys(data.textVersions), null, 2)}</pre> */}
        <div className="level">
            {character?.image?.public_path && <div className="level-item"></div>}
            <div className="level-item" style={{ width: "200px" }}>{character?.image?.public_path && <img src={character?.image?.public_path} style={characterSmallImageStyle} />}<span className="ml-3">{character.name}</span></div>
            <div className="level-item" style={{ width: "300px" }}>
                <div className="field has-addons">
                    <div className="control">
                        <div className="select">
                            <select value={selectedTextVersion} onChange={(e) => { handleTextVersionChange(e) }}>
                                <option value={1}>Version officielle</option>
                                {Object.values(data.textVersions).map((v, index) => { return (<option key={index} value={v.id}>{v.name}</option>) })}
                                {userId != "undefined" && <option value={-1}>Nouvelle version</option>}
                            </select>
                        </div>
                    </div>
                    {(selectedTextVersion != 1 && textVersions.byIds[selectedTextVersion]?.creator_id == userId) && (<div className="control">
                        <button className="button is-danger" onClick={removeCharacterTextVersion}><span className="fas fa-trash"></span></button>
                    </div>)}
                </div>
            </div>
            <div className="level-item" style={{ width: "300px" }}>
                <div className="field has-addons">
                    <div className="control">
                        <div className="select">
                            <select value={selectedAudioVersion} onChange={(e) => { handleAudioVersionChange(e) }}>
                                {userId != "undefined" && <option value={-1}>Enregistrer</option>}
                                <option value={-2}>Voix robotisé</option>
                                {Object.values(data.audioVersions).map((v) => { return (<option key={v.id} value={v.id}>{`${v.name} (${v.audios} audios)`}</option>) })}
                            </select>
                        </div>
                    </div>

                    {(selectedAudioVersion > 0 && audioVersions.byIds[selectedAudioVersion]?.creator_id == userId) && (<div className="control">
                        <button className="button is-danger" onClick={removeCharacterAudioVersion}><span className="fas fa-trash"></span></button>
                    </div>)}

                </div>

            </div>
        </div>
    </>)
}

const mapStateToProps = (state) => {
    return {
        sceneId: state.scenes.selectedId,
        lines: state.lines,
        characters: state.characters,
        audioVersions: state.audioVersions,
        userId: state.miscellaneous.user.userId,
        textVersions: state.textVersions
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        selectCharacterAudioVersion: (characterId, audioVersionId) => {
            dispatch(selectCharacterAudioVersion(characterId, audioVersionId));
        },
        selectCharacterTextVersion: (characterId, textVersionId) => {
            dispatch(selectCharacterTextVersion(characterId, textVersionId));
        },
        createCharacterTextVersion: (characterId, textVersionId) => {
            dispatch(createCharacterTextVersion(characterId, textVersionId));
        },
        removeCharacterTextVersion: (characterId, textVersionId) => {
            dispatch(removeCharacterTextVersion(characterId, textVersionId));
        },
        removeCharacterAudioVersion: (characterId, audioVersionId) => {
            dispatch(removeCharacterAudioVersion(characterId, audioVersionId));
        }
    };
};



export default connect(mapStateToProps, mapDispatchToProps)(CharacterUserVersion);
