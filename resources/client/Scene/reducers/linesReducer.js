const refactor_lines = (lines) => {
    console.log(lines);
    let byIds = {};
    let ids = [];
    console.log(lines);
    lines.sort((a, b) => { return (a.position - b.position) });

    lines.forEach(element => {
        byIds = { ...byIds, [element.id]: element };
        ids.push(element.id);
    });
    return { byIds, ids };
}
const linesReducer = (state = [], action) => {
    let lineId;
    let characterId;
    switch (action.type) {
        case "ADD_LINE":

            state = refactor_lines(action.payload.lines)
            break
        case "DELETE_LINE":
            console.log(action.payload);
            state = refactor_lines(action.payload.lines)
            break
        case "UPDATE_TEXT":
            action.payload;
            state = {
                ...state,
                byIds: {
                    ...state.byIds,
                    [action.payload.lineId]: { ...state.byIds[action.payload.lineId], text: action.payload.text }
                }
            }
            break
        case "CHARACTER_SELECT":
            let { characterId, lineId } = action.payload;
            state = {
                ...state,
                byIds: {
                    ...state.byIds,
                    [lineId]: { ...state.byIds[lineId], character_id: characterId }
                }
            }
            break;
        case "DETACH_CHARACTER":
            const byIds = Object.values(state.byIds).reduce((acc, curr) => {
                console.log("currCharacterAvant=", curr);
                if (curr.character_id == action.payload.characterId) {
                    curr = { ...curr, character_id: null };
                }
                console.log("currCharacterApres=", curr);
                return { ...acc, [curr.id]: curr }
            }, {})
            console.log("final", byIds);
            state = {
                ...state,
                byIds: byIds
            };
            break
        case "ADD_CHARACTER":
            state = {
                ...state,
                byIds: {
                    ...state.byIds,
                    [action.payload.lineId]: { ...state.byIds[action.payload.lineId], character_id: action.payload.character.id }
                }
            }
            break
        case "LOAD_LINES":
            console.log('payload.lines:' + action.payload.lines)
            state = refactor_lines(action.payload.lines)
            break
        case "SPLIT_LINE":
            console.log(action.payload);
            let newLine = action.payload.newLine;
            lineId = action.payload.lineId;
            let newLineId = newLine.id;
            let ids = [...state.ids];
            ids.splice(ids.indexOf(lineId) + 1, 0, newLineId);
            state = {
                ...state,
                byIds: {
                    ...state.byIds,
                    [action.payload.lineId]: {
                        ...state.byIds[action.payload.lineId],
                        text: action.payload.firstPart
                    },
                    [newLineId]: newLine
                },
                ids: ids
            };
            break
        case "SELECT_LINE":
            state = { ...state, selectedId: action.payload.lineId }
            break
        case "PLAY_LINE":
            state = {
                ...state,
                selectedId: action.payload.lineId,
                action: "play"
            }
            break
        case "SET_LINE_ACTION":
            state = {
                ...state,
                action: action.payload.action,
                selectedId: action.payload.lineId
            }
            break

    }
    return state
}

export default linesReducer;