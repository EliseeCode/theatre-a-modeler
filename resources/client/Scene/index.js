import React from 'react';
import ReactDOM from 'react-dom';
import {
    BrowserRouter as Router,
    Routes,
    Route
} from "react-router-dom";

import EditScene from './EditScene';
ReactDOM.render(
    <Router>
        <Routes>
            <Route path="/scene/:sceneId/edit" element={<EditScene />}>
            </Route>
        </Routes>
    </Router>,
    document.getElementById('root')
);