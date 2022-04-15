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
const versionsReducer = (state = null, action) => {
    switch (action.type) {
        case "LOAD_AUDIO":
            var state = refactor_versions(action.payload.versions);
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
        case "REMOVE_VERSION":
            var state = {
                ...state,
                ids: [...state.ids.filter((id) => { return id != action.payload.versionId })],
            }
            console.log(state);
            break
    }
    return state
}

export default versionsReducer;