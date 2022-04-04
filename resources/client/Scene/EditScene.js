import LinesContainer from "./components/LinesContainer"
import { Provider } from "react-redux"
import store from "./store"
import React from "react"

export default function EditScene() {

    return (
        <Provider store={store}>
            <LinesContainer />
        </Provider>
    )
}