import { reducer } from "redux"

const charactersReducer = (state = null, action) => {
    switch (action.type) {
        case "LOAD_CHARACTERS":
            state = {
                ...state,
                characters: action.payload
            };
            break
        case "ADD_CHARACTER":
            state = {
                ...state,
                characters: [...state.characters, action.payload]
            }
            break
    }
    return state
}

export default charactersReducer;