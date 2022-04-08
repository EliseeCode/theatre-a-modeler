const refactor_audios = (audios) => {
    console.log(audios);
    let byIds = {};
    let ids = [];
    audios.sort((a, b) => { a.position - b.position });

    audios.forEach(element => {
        byIds = { ...byIds, [element.id]: element };
        ids.push(element.id);
    });
    return { byIds, ids };
}
const audiosReducer = (state = null, action) => {
    switch (action.type) {
        case "LOAD_AUDIO":
            state = action.payload
            break
    }
    return state
}

export default audiosReducer;