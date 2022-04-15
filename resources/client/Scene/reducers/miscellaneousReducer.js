const miscellaneousReducer = (state = null, action) => {
    switch (action.type) {
        case "LOAD_CSFR":
            state = { ...state, csfr: action.payload }
            break
        case "LOAD_USER_ID":
            state = { ...state, user: action.payload }
            break
    }
    return state
}

export default miscellaneousReducer;