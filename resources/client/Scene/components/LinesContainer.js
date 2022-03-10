import React, { useState, useEffect } from 'react'
import EditableLine from './EditableLine.js'
import NewLineButton from './NewLineButton'

import { useParams } from 'react-router-dom';
export default function LinesContainer(props) {
    const { sceneId } = useParams();
    console.log(sceneId);
    const [lines, setLines] = useState([]);
    const [scene, setScene] = useState({});
    const [characters, setCharacters] = useState([]);

    //load all lines data when initialising.


    useEffect(() => {
        $.get('/api/scene/' + sceneId + '/version/1/lines', function (data) {
            console.log(data);
            setLines(data.lines);
            setCharacters(data.characters);
            setScene(data.scene);
        })
    }, []);

    return (
        <div>
            <NewLineButton afterPosition="-1" />
            {
                lines
                    .map((line, index) => {
                        return (
                            <EditableLine key={index} setLines={setLines} line={line} characters={characters} />
                        );
                    })
            }



        </div >
    )
}