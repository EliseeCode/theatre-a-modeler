import React, { useState, useEffect } from 'react'
import EditableLine from './EditableLine.js'
import Character from './Character.js'
import { useParams } from 'react-router';
import { initialLoadLine, addLine } from "../actions/lineAction";
import { initialLoadSceneId } from "../actions/sceneAction";
import { detachCharacter } from "../actions/characterAction";
import { connect } from "react-redux";
import NewLineButton from './NewLineButton';

const LinesContainer = (props) => {

    const { sceneId } = useParams();

    useEffect(() => {
        props.initialLoadSceneId(sceneId);
        props.initialLoadLine(sceneId);
    }, [])


    return (
        <div style={{ display: 'inline-block' }}>
            {props.characters?.ids.length != 0 && (
                <div class="box mb-1">
                    <div class="subtitle block">Personnages :</div>
                    <div class="field is-grouped is-grouped-multiline block">
                        {props.characters?.ids.map((characterId) => {
                            return (
                                <Character key={characterId} sceneId={sceneId} detachCharacter={props.detachCharacter} characters={props.characters} characterId={characterId} />
                            );
                        })
                        }
                    </div>
                </div>)}
            {props.lines.ids.length == 0 && <NewLineButton addLine={props.addLine} sceneId={props.sceneId} afterLinePos={-1} />}
            {
                props.lines?.ids.map((lineId) => {
                    return (
                        <EditableLine key={lineId} lineId={lineId} />
                    );
                })
            }
        </div >
    )
}

const mapStateToProps = (state) => {
    return {
        lines: state.lines,
        sceneId: state.scene.id,
        characters: state.characters
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        initialLoadLine: (sceneId) => {
            dispatch(initialLoadLine(sceneId));
        },
        initialLoadSceneId: (sceneId) => {
            dispatch(initialLoadSceneId(sceneId));
        },
        addLine: (afterLinePos, sceneId) => {
            dispatch(addLine(afterLinePos, sceneId));
        },
        detachCharacter: (characterId, sceneId) => {
            dispatch(detachCharacter(characterId, sceneId));
        }
    };
};




export default connect(mapStateToProps, mapDispatchToProps)(LinesContainer);