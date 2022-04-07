const refactor_scenes = (scenes) => {
    console.log(scenes);
    let byIds = {};
    let ids = [];
    scenes.sort((a, b) => { a.position - b.position });

    scenes.forEach(element => {
        byIds = { ...byIds, [element.id]: element };
        ids.push(element.id);
    });
    return { byIds, ids };
}
const scenesReducer = (state = [], action) => {
    switch (action.type) {

        case "LOAD_SCENES":
            console.log('payload.lines:' + action.payload.scenes)
            state = refactor_scenes(action.payload.scenes)
            break

        case "LOAD_SCENEID":
            state = {
                ...state,
                selectedId: action.payload
            };
            break
    }
    return state
}

export default scenesReducer;