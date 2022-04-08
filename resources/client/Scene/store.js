import { createStore, combineReducers, applyMiddleware } from "redux";
import logger from "redux-logger";
import thunk from "redux-thunk";

import lines from "./reducers/linesReducer";
import characters from "./reducers/charactersReducer";
import play from "./reducers/playReducer";
import scenes from "./reducers/scenesReducer";
import audios from "./reducers/audiosReducer";

export default createStore(
    combineReducers({
        lines,
        characters,
        scenes,
        play,
        audios
    }),
    {
        lines: { byIds: {}, ids: [] },
        characters: { byIds: {}, ids: [] },
        scenes: { byIds: {}, ids: [], selectedId: null },
        play: {},
        audios: { byIds: {}, ids: [] },
    },
    applyMiddleware(logger, thunk)
);