import React from 'react'

export default function NewLineButton(props) {
    function NewLineHandler() {
        props.addLine(props.afterLinePos, props.sceneId);
    }

    return (
        <div className="field buttonAddLine">
            <button onClick={NewLineHandler} className="fas fa-plus" type="submit" title="Ajouter une réplique ici"></button>
        </div>
    )
}
