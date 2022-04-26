const refactor_audios = (audios) => {
    console.log(audios);
    let byIds = {};
    let ids = [];

    audios.forEach(element => {
        byIds = { ...byIds, [element.id]: element };
        ids.push(element.id);
    });
    return { byIds, ids };
}
const audiosReducer = (state = null, action) => {
    switch (action.type) {
        case "LOAD_AUDIO":
            var state = refactor_audios(action.payload.audios);
            break
        case "ADD_AUDIO":
            var newAudio = action.payload.audio;
            console.log(action);
            var state = {
                ...state,
                byIds: { ...state.byIds, [newAudio.id]: newAudio },
                ids: [...state.ids, newAudio.id]
            }
            break
        case "REMOVE_AUDIO":

            var state = {
                ...state,
                ids: [...state.ids.filter((id) => { return id != action.payload.audioId })],
            }
            delete state.byIds[action.payload.audioId];
            console.log(state);
            break
        case "SET_AUTOPLAY":
            state = { ...state, autoplay: action.payload.isAutoplay }
            break
    }
    return state
}

export default audiosReducer;