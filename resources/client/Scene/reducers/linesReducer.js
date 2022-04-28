const refactor_lines = (lines) => {
    console.log(lines);
    let byIds = {};
    let ids = [];
    let officialLine;
    console.log(lines);
    officialLine = lines.filter((line) => { return line.version_id == 1; });
    officialLine.sort((a, b) => { return (a.position - b.position) });

    lines.forEach(element => {
        byIds = { ...byIds, [element.id]: element };
    });
    officialLine.forEach(element => {
        ids.push(element.id);
    });
    return { byIds, ids };
}
const linesReducer = (state = [], action) => {
    let lineId, newLine, newLineId;
    let ids, byIds;
    let characterId, textVersionId;
    let newLines;
    switch (action.type) {
        case "ADD_LINE":

            state = { ...state, ...refactor_lines(action.payload.lines) }
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
            ({ characterId, lineId } = action.payload);
            state = {
                ...state,
                byIds: {
                    ...state.byIds,
                    [lineId]: { ...state.byIds[lineId], character_id: characterId }
                }
            }
            break;
        case "DETACH_CHARACTER":
            byIds = Object.values(state.byIds).reduce((acc, curr) => {
                console.log("currCharacterAvant=", curr);
                if (curr.character_id == action.payload.characterId) {
                    curr = { ...curr, character_id: null };
                }
                console.log("currCharacterApres=", curr);
                return { ...acc, [curr.id]: curr }
            }, {})
            state = {
                ...state,
                byIds: byIds
            };
            break
        case "UPDATE_CHARACTER":
            state = {
                ...state,
                byIds: {
                    ...state.byIds,
                    [action.payload.lineId]: { ...state.byIds[action.payload.lineId], character_id: action.payload.character.id }
                }
            }
            break
        case "LOAD_LINES":
            state = { ...state, ...refactor_lines(action.payload.lines) }
            break
        case "SPLIT_LINE":
            console.log(action.payload);
            newLine = action.payload.newLine;
            lineId = action.payload.lineId;
            newLineId = newLine.id;
            ids = [...state.ids];
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
            if (state.selectedId != action.payload.lineId) {
                state = { ...state, selectedId: action.payload.lineId }
            }
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
                //selectedId: action.payload.lineId
            }
            break
        case "CREATE_CHARACTER_TEXT_VERSION":
            characterId = action.payload.characterId;
            textVersionId = action.payload.version.id;
            newLines = action.payload.lines;
            byIds = {
                ...state.byIds,
                ...newLines.reduce((acc, line) => { return { ...acc, [line.id]: line }; }, {})
            }
            //replace line by the position of the new one.
            ids = [...state.ids];
            newLines.forEach((line) => {
                ids.splice(line.position, 1, line.id);
            })
            state = {
                ...state,
                ids: ids,
                byIds: byIds
            }
            break
        case "SELECT_CHARACTER_TEXT_VERSION":
            characterId = action.payload.characterId;
            textVersionId = action.payload.textVersionId;
            newLines = Object.values(state.byIds).filter((line) => { return (line.character_id == characterId && line.version_id == textVersionId) });
            //replace line by the position of the new one.
            ids = [...state.ids];
            newLines.forEach((line) => {
                ids.splice(line.position, 1, line.id);
            })
            state = {
                ...state,
                ids: ids
            }
            break

    }
    return state
}

export default linesReducer;