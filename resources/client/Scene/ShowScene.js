import PlaySceneSelector from "./components/PlaySceneSelector";
import CharacterUserVersionContainer from "./components/CharacterUserVersionContainer";
import LinesShowContainer from "./components/LinesShowContainer";
import DataLoaderSceneShow from "./components/DataLoaderSceneShow";
import { Provider } from "react-redux";
import store from "./store";
import React from "react";
import AudioReader from "./components/AudioReader";

export default function ShowScene(props) {

    const lineContainerStyle = {
        backgroundColor: '#f4f4f4',
    }
    return (
        <Provider store={store}>
            <AudioReader />
            <PlaySceneSelector editContext={false} />

            <div className="container" style={lineContainerStyle}>
                <DataLoaderSceneShow />


                <CharacterUserVersionContainer />
                <LinesShowContainer />
            </div>
        </Provider>
    );


}
