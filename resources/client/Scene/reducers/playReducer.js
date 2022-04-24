const playReducer = (state = null, action) => {
    switch (action.type) {
        case "LOAD_PLAY":
            state = { ...state, ...action.payload }
            break
    }
    return state
}

export default playReducer;