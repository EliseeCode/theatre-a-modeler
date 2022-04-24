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
const audioVersionsReducer = (state = null, action) => {
    switch (action.type) {
        case "LOAD_AUDIO":
            var state = { ...state, ...refactor_versions(action.payload.versions) };
            break
        case "ADD_AUDIO":
            var newVersion = action.payload.version;
            console.log(action);
            var state = {
                ...state,
                byIds: { ...state.byIds, [newVersion.id]: newVersion },
                ids: [...state.ids, newVersion.id]
            }
            break
        case "REMOVE_CHARACTER_AUDIO_VERSION":
            state = {
                ...state,
                ids: [...state.ids.filter((id) => { return id != action.payload.audioVersionId })],
            }
            console.log(state);
            break
    }
    return state
}

export default audioVersionsReducer;