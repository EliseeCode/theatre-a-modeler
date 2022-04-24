const refactor_versions = (versions) => {
    console.log(versions);
    let byIds = {};
    let ids = [];

    versions.forEach(element => {
        byIds = { ...byIds, [element.id]: element };
        ids.push(element.id);
    });
    return { byIds, ids };
}
const textVersionsReducer = (state = null, action) => {
    let textVersion, characterId;
    switch (action.type) {

        case "LOAD_TEXT_VERSIONS":
            state = { ...state, ...refactor_versions(action.payload.textVersions) };
            break
        case "ADD_TEXT_VERSIONS":
            var newVersion = action.payload.version;
            console.log(action);
            state = {
                ...state,
                byIds: { ...state.byIds, [newVersion.id]: newVersion },
                ids: [...state.ids, newVersion.id]
            }
            break
        case "REMOVE_CHARACTER_TEXT_VERSION":
            state = {
                ...state,
                ids: [...state.ids.filter((id) => { return id != action.payload.textVersionId })],
            }
            console.log(state);
            break
        case "CREATE_CHARACTER_TEXT_VERSION":
            textVersion = action.payload.version;
            characterId = action.payload.characterId;
            state = {
                ...state,
                byIds: { ...state.byIds, [textVersion.id]: { ...textVersion, character_id: characterId } },
                ids: [...state.ids, textVersion.id]
            }
            break
    }
    return state
}

export default textVersionsReducer;