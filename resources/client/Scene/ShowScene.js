import PlaySceneSelector from "./components/PlaySceneSelector";
import { Provider } from "react-redux";
import store from "./store";
import React from "react";

export default function ShowScene(props) {
    return (
        <Provider store={store}>
            <div className="container mt-3 mb-3">
                <PlaySceneSelector />
            </div>
        </Provider>
    )
}
