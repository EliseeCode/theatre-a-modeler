import React, { useState, useEffect } from 'react'
import EditableLine from './EditableLine.js'
import NewLineButton from './NewLineButton'

export default function LinesContainer() {
    const [lines, setLines] = useState([]);
    const [characters, setCharacters] = useState([]);
    useEffect(() => {
        $.get('/api/scene/1/version/1/lines', function (data) {
            console.log(data);
            setLines(data.lines)
            setCharacters(data.characters)
        })
    }, []);

    return (
        <div>
            <NewLineButton afterPosition="-1" />
            {
                lines.map((line, index) => {
                    return (
                        <EditableLine key={index} line={line} characters={characters} />
                    );
                })
            }
        </div >
    )
}