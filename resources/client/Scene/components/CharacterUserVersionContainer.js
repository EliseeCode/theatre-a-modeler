
import React, { useState, useRef, useEffect } from 'react'
import CharacterUserVersion from './CharacterUserVersion';
import { selectCharacter } from "../actions/charactersAction";
import { connect } from "react-redux"
import listenForOutsideClick from '../helper/listenerOutsideClick';

const CharacterUserVersionContainer = (props) => {
    const { lineId, lines, characters } = props;

    return (<div className="box block">
        CharacterUserVersionContainer
        {characters.ids.map((characterId) => {
            return <CharacterUserVersion key={characterId} character={characters} characterId={characterId} />
        })}
    </div>)
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



export default connect(mapStateToProps, mapDispatchToProps)(CharacterUserVersionContainer);
