
const refactor_characters = (characters) => {
    console.log(characters);
    let byIds = {};
    let ids = [];
    console.log(characters);
    characters.sort((a, b) => { a.position - b.position });

    characters.forEach(element => {
        byIds = { ...byIds, [element.id]: element };
        ids.push(element.id);
    });
    return { byIds, ids };
}

const charactersReducer = (state = null, action) => {
    switch (action.type) {
        case "LOAD_CHARACTERS":
            state = {
                ...state,
                characters: action.payload
            };
            break
        case "DETACH_CHARACTER":
            state = {
                ...state,
                ids: [...state.ids.filter((character_id) => { return action.payload.characterId != character_id; })]
            };
            break
        case "ADD_CHARACTER":
            state = {
                ...state,
                byIds: {
                    ...state.byIds,
                    [action.payload.character.id]: action.payload.character
                },
                ids: [...state.ids, action.payload.character.id]
            }
            break

        case "LOAD_CHARACTER":
            state = refactor_characters(action.payload.characters)
            break
    }
    return state
}

export default charactersReducer;