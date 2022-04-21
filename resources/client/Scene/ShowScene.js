import PlaySceneSelector from "./components/PlaySceneSelector";
import CharacterUserVersionContainer from "./components/CharacterUserVersionContainer";
import LinesShowContainer from "./components/LinesShowContainer";
import DataLoaderSceneShow from "./components/DataLoaderSceneShow";
import { Provider } from "react-redux";
import store from "./store";
import React from "react";
import AudioReader from "./components/AudioReader";

export default function ShowScene(props) {


    return (
        <Provider store={store}>
            <div className="container mt-3 mb-3">
                <DataLoaderSceneShow />
                <PlaySceneSelector editContext={false} />
                <AudioReader />
                <CharacterUserVersionContainer />
                <LinesShowContainer />
            </div>
        </Provider>
    );


}
