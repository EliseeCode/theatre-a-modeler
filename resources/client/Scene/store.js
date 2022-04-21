import { createStore, combineReducers, applyMiddleware } from "redux";
import logger from "redux-logger";
import thunk from "redux-thunk";

import lines from "./reducers/linesReducer";
import characters from "./reducers/charactersReducer";
import play from "./reducers/playReducer";
import scenes from "./reducers/scenesReducer";
import audios from "./reducers/audiosReducer";
import versions from "./reducers/versionsReducer";
import miscellaneous from "./reducers/miscellaneousReducer";

export default createStore(
    combineReducers({
        lines,
        characters,
        scenes,
        play,
        audios,
        versions,
        miscellaneous
    }),
    {
        lines: { byIds: {}, ids: [], selectedId: null, action: null },
        characters: { byIds: {}, ids: [] },
        scenes: { byIds: {}, ids: [], selectedId: null },
        play: {},
        audios: { byIds: {}, ids: [], selectedId: null },
        versions: { byIds: {}, ids: [] },
        miscellaneous: { csfr: '', user_id: 'undefined' }
    },
    applyMiddleware(logger, thunk)
);