import React, { useEffect } from 'react'
import EditableLine from './EditableLine.js'
import Character from './Character.js'
import { useParams } from 'react-router';
import { initialLoadOfficialLines, addLine } from "../actions/linesAction";
import { initialLoadSceneId } from "../actions/sceneAction";
import { detachCharacter } from "../actions/charactersAction";
import { connect } from "react-redux";
import NewLineButton from './NewLineButton';

const LinesContainer = (props) => {

    const { sceneId } = useParams();

    useEffect(() => {
        props.initialLoadSceneId(sceneId);
        props.initialLoadOfficialLines(sceneId);
    }, [])


    return (
        <div style={{ display: 'inline-block' }}>
            {props.characters?.ids.length != 0 && (
                <div className="box mb-1">
                    <div className="subtitle block">Personnages :</div>
                    <div className="field is-grouped is-grouped-multiline block">
                        {props.characters?.ids.map((characterId) => {
                            return (
                                <Character key={characterId} sceneId={sceneId} detachCharacter={props.detachCharacter} characters={props.characters} characterId={characterId} />
                            );
                        })
                        }
                    </div>
                </div>)}

            {props.lines.ids.length == 0 && <NewLineButton addLine={props.addLine} sceneId={sceneId} afterLinePos={-1} />}
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
        characters: state.characters,
        sceneId: state.scenes.selectedId
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        initialLoadOfficialLines: (sceneId) => {
            dispatch(initialLoadOfficialLines(sceneId));
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