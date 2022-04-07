import React from 'react';
import ReactDOM from 'react-dom';
import {
    BrowserRouter as Router,
    Routes,
    Route
} from "react-router-dom";

import EditScene from './EditScene';
import Scene from './Scene';

ReactDOM.render(
    <Router>
        <Routes>
            <Route path="/scene/:sceneId/edit" element={<EditScene />}>
            </Route>
            <Route path="/scene/:sceneId" element={<Scene />}>
            </Route>
        </Routes>
    </Router>,
    document.getElementById('root')
);