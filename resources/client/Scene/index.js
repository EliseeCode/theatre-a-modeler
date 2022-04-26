import React from 'react';
import ReactDOM from 'react-dom';
import {
    BrowserRouter as Router,
    Routes,
    Route
} from "react-router-dom";

import EditScene from './EditScene';
import ShowScene from './ShowScene';

ReactDOM.render(
    <Router>
        <Routes>
            <Route path="/scene/:sceneId/edit" element={<EditScene />}>
            </Route>
            <Route path="/group/:groupId/scene/:sceneId/edit" element={<EditScene />}>
            </Route>
            <Route path="/scene/:sceneId" element={<ShowScene />}>
            </Route>
            <Route path="/group/:groupId/scene/:sceneId" element={<ShowScene />}>
            </Route>
        </Routes>
    </Router>,
    document.getElementById('root')
);