
import React, { useState, useRef, useEffect } from 'react'
import CharacterUserVersion from './CharacterUserVersion';
import { selectCharacter } from "../actions/charactersAction";
import { connect } from "react-redux"

const CharacterUserVersionContainer = (props) => {
    const { lineId, lines, characters, audios } = props;

    return (<div className="box block">
        <div style={{ maxWidth: '500px', display: "inline-block" }}>
            <h2 className="subtitle">Choix des interprètes</h2>
            {characters.ids.map((characterId) => {
                return <CharacterUserVersion key={characterId} audios={audios} character={characters} characterId={characterId} />
            })}
        </div>
    </div >)
}


const mapStateToProps = (state) => {
    return {
        sceneId: state.scenes.selectedId,
        lines: state.lines,
        characters: state.characters,
        audios: state.audios,
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
