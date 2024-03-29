import LinesContainer from "./components/LinesContainer";
import PlaySceneSelector from "./components/PlaySceneSelector";
import { Provider } from "react-redux";
import store from "./store";
import React from "react";

export default function EditScene(props) {
    return (
        <Provider store={store}>
            <PlaySceneSelector editContext={true} />
            <LinesContainer />
        </Provider>
    )
}
