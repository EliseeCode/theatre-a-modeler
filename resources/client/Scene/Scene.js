import LinesContainer from "./components/LinesContainer";
import { Provider } from "react-redux";
import store from "./store";
import React from "react";

export default function Scene(props) {
    return (
        <Provider store={store}>
            <LinesContainer />
        </Provider>
    )
}
