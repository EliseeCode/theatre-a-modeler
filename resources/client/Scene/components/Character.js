import React from 'react'

export default function Character(props) {

    return (
        <div className="control">
            <div className="tags has-addons">
                <a className="tag is-link" href={"/characters/" + props.characterId}>{props.characters.byIds[props.characterId]?.name}</a>
                <a className="tag is-delete" onClick={() => { props.detachCharacter(props.characterId, props.sceneId) }}></a>
            </div>
        </div>
    )
}

