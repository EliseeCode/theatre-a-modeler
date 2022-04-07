import { reducer } from "redux"

const sceneReducer = (state = null, action) => {
    switch (action.type) {
        case "LOAD_SCENE":
            state = {
                ...state,
                scene: action.payload
            };
            break
        case "LOAD_SCENEID":
            state = {
                ...state,
                id: action.payload
            };
            break
    }
    return state
}

export default sceneReducer;