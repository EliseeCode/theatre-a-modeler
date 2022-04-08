
import React, { useState, useRef, useEffect } from 'react'
import { selectCharacter } from "../actions/charactersAction";
import { connect } from "react-redux"

const CharacterUserVersion = (props) => {
    const { characters, characterId } = props;
    const character = characters.byIds[characterId];
    return (<div className="level">{character.name}</div>)
}

const mapStateToProps = (state) => {
    return {
        sceneId: state.scenes.selectedId,
        lines: state.lines,
        characters: state.characters
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        selectCharacter: (characterId, lineId) => {
            dispatch(selectCharacter(characterId, lineId));
        }
    };
};



export default connect(mapStateToProps, mapDispatchToProps)(CharacterUserVersion);
