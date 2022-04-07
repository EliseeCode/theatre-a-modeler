import React from 'react'

export default function Character(props) {

    return (
        <div class="control">
            <div class="tags has-addons">
                <a class="tag is-link" href={"/characters/" + props.characterId}>{props.characters.byIds[props.characterId].name}</a>
                <a class="tag is-delete" onClick={() => { props.detachCharacter(props.characterId, props.sceneId) }}></a>
            </div>
        </div>
    )
}

