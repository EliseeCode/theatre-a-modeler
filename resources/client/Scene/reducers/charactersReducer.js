
const refactor_characters = (characters) => {
    console.log(characters);
    let byIds = {};
    let ids = [];
    console.log(characters);
    characters.sort((a, b) => { a.position - b.position });

    characters.forEach(element => {
        byIds = { ...byIds, [element.id]: { ...element, selectedAudioVersion: -2 } };
        ids.push(element.id);
    });
    return { byIds, ids };
}
function changeCharacterAudioVersion(state, characterId, audioVersionId) {
    console.log("inside", characterId, audioVersionId);
    var state = {
        ...state,
        byIds: {
            ...state.byIds,
            [characterId]: {
                ...state.byIds[characterId],
                selectedAudioVersion: audioVersionId
            }
        }
    }
    console.log("state", state);
    return state;
}
function changeCharacterTextVersion(state, characterId, textVersionId) {
    console.log("inside", characterId, textVersionId);
    var state = {
        ...state,
        byIds: {
            ...state.byIds,
            [characterId]: {
                ...state.byIds[characterId],
                selectedTextVersion: textVersionId
            }
        }
    }
    console.log("state", state);
    return state;
}

const charactersReducer = (state = null, action) => {
    let characterId;
    let audioVersionId, textVersionId;
    let audios;
    let audio_id;
    switch (action.type) {
        case "DETACH_CHARACTER":
            state = {
                ...state,
                ids: [...state.ids.filter((character_id) => { return action.payload.characterId != character_id; })]
            };
            break
        case "UPDATE_CHARACTER":
            state = {
                ...state,
                byIds: {
                    ...state.byIds,
                    [action.payload.character.id]: { ...action.payload.character, selectedAudioVersion: -1 }
                }
            }
            if (state.ids.indexOf(action.payload.character.id) == -1) {
                state = {
                    ...state,
                    ids: [...state.ids, action.payload.character.id]
                }
            }
            break

        case "LOAD_CHARACTERS":
            state = { ...state, ...refactor_characters(action.payload.characters) }
            break
        case "SELECT_CHARACTER_AUDIO_VERSION":
            characterId = action.payload.characterId;
            audioVersionId = action.payload.audioVersionId;
            state = changeCharacterAudioVersion(state, characterId, audioVersionId)
            break
        case "SELECT_CHARACTER_TEXT_VERSION":
            characterId = action.payload.characterId;
            textVersionId = action.payload.textVersionId;
            state = changeCharacterTextVersion(state, characterId, textVersionId)
            break
        case "CREATE_CHARACTER_TEXT_VERSION":
            characterId = action.payload.characterId;
            textVersionId = action.payload.version.id;
            state = changeCharacterTextVersion(state, characterId, textVersionId)
            break
        case "ADD_AUDIO":
            characterId = action.payload.audio.line.character_id;
            audioVersionId = parseInt(action.payload.version.id);
            console.log("addAudioUpdateVersion", characterId, audioVersionId);
            state = changeCharacterAudioVersion(state, characterId, audioVersionId)
            break;
        case "REMOVE_AUDIO":
            let audio_id = action.payload.audioId;
            let audios = action.payload.audios;
            let audio = audios.byIds[audio_id];
            let characterId = action.payload.characterId;

            let versionAudio = audios.byIds[audio_id].version_id;
            var listAudioVersionId = Object.values(audios.byIds)
                .filter((audio) => { return audio.id != audio_id })
                .map((audio) => { return audio.version_id });
            if (!listAudioVersionId.includes(versionAudio)) {
                state = changeCharacterAudioVersion(state, characterId, -2)
            }
            break
        case "REMOVE_CHARACTER_AUDIO_VERSION":
            characterId = action.payload.characterId;
            state = changeCharacterAudioVersion(state, characterId, -2)
            break;
    }
    return state
}

export default charactersReducer;