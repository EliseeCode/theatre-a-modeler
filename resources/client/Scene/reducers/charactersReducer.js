
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

const charactersReducer = (state = null, action) => {
    let characterId;
    let audioVersionId;
    switch (action.type) {
        case "DETACH_CHARACTER":
            state = {
                ...state,
                ids: [...state.ids.filter((character_id) => { return action.payload.characterId != character_id; })]
            };
            break
        case "ADD_CHARACTER":
            state = {
                ...state,
                byIds: {
                    ...state.byIds,
                    [action.payload.character.id]: { ...action.payload.character, selectedAudioVersion: -1 }
                },
                ids: [...state.ids, action.payload.character.id]
            }
            break

        case "LOAD_CHARACTERS":
            state = refactor_characters(action.payload.characters)
            break
        case "SELECT_CHARACTER_AUDIO_VERSION":
            characterId = action.payload.characterId;
            audioVersionId = action.payload.audioVersionId;
            state = changeCharacterAudioVersion(state, characterId, audioVersionId)
            break
        case "ADD_AUDIO":
            characterId = action.payload.audio.line.character_id;
            audioVersionId = parseInt(action.payload.version.id);
            console.log("addAudioUpdateVersion", characterId, audioVersionId);
            state = changeCharacterAudioVersion(state, characterId, audioVersionId)
            break;
    }
    return state
}

export default charactersReducer;