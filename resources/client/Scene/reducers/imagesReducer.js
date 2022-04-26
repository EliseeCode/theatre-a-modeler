
const imagesReducer = (state = null, action) => {
    switch (action.type) {
        case "LOAD_IMAGES":
            var state = {
                ...state,
                coverImages: [...action.payload.coverImages],
                characterImages: [...action.payload.characterImages]
            }
            break
    }
    return state
}

export default imagesReducer;