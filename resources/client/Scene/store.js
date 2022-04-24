import { createStore, combineReducers, applyMiddleware } from "redux";
import logger from "redux-logger";
import thunk from "redux-thunk";

import lines from "./reducers/linesReducer";
import characters from "./reducers/charactersReducer";
import play from "./reducers/playReducer";
import scenes from "./reducers/scenesReducer";
import audios from "./reducers/audiosReducer";
import audioVersions from "./reducers/audioVersionsReducer";
import textVersions from "./reducers/textVersionsReducer";
import miscellaneous from "./reducers/miscellaneousReducer";

export default createStore(
    combineReducers({
        lines,
        characters,
        scenes,
        play,
        audios,
        audioVersions,
        textVersions,
        miscellaneous
    }),
    {
        lines: { byIds: {}, ids: [], selectedId: null, action: null },
        characters: { byIds: {}, ids: [] },
        scenes: { byIds: {}, ids: [], selectedId: null },
        play: {},
        audios: { byIds: {}, ids: [], selectedId: null },
        audioVersions: { byIds: {}, ids: [] },
        textVersions: { byIds: {}, ids: [] },
        miscellaneous: { csfr: '', user: { userId: 'undefined' } }
    },
    applyMiddleware(logger, thunk)
);