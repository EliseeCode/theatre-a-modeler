import { reducer } from "redux"

const linesReducer = (state = [], action) => {
    switch (action.type) {
        case "ADD_LINE":
            state = [
                ...state,
                action.payload
            ]
            break
        case "DELETE_LINE":
            state = {
                ...state,
                lines: [...state.lines.filter((line) => { return line.id != action.payload.lineId })]
            }
            break
        case "UPDATE_TEXT":
            state = {
                ...state,
                lines: [...state.lines, action.payload]
            }
            break
        case "LOAD_LINE":
            console.log('payload.lines:' + action.payload.lines)
            state = [
                ...state,
                ...action.payload.lines,
            ];
            break
    }
    return state
}

export default linesReducer;