import React, { Fragment } from 'react';
import ReactDOM from 'react-dom';
import {
    BrowserRouter as Router,
    Routes,
    Route
} from "react-router-dom";

import LinesContainer from './components/LinesContainer';
ReactDOM.render(
    <Router>
        <Routes>
            <Route path="/scene/:sceneId/edit" element={<LinesContainer />}>

            </Route>
            <Route path="/scene/:sceneId" element={<LinesContainer />}>

            </Route>
        </Routes>
    </Router>,
    document.getElementById('root')
);