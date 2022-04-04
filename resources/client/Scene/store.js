import { createStore, combineReducers, applyMiddleware } from "redux";
import logger from "redux-logger";
import thunk from "redux-thunk";

import lines from "./reducers/linesReducer";
import characters from "./reducers/charactersReducer";
import scene from "./reducers/sceneReducer";

export default createStore(
    combineReducers({
        lines,
        characters,
        scene
    }),
    {
        lines: [],
        characters: [],
        scene: {}
    },
    applyMiddleware(logger, thunk)
);