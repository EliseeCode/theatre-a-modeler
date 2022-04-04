import React, { useState, useEffect } from 'react'
import EditableLine from './EditableLine.js'
import { useParams } from 'react-router-dom';
import { initialLoadLine } from "../actions/lineAction";
import { connect } from "react-redux"

const LinesContainer = (props) => {
    const { sceneId } = useParams();
    useEffect(() => {
        props.initialLoadLine(sceneId);
    }, [])
    console.log(props)
    return (
        <div>
            Hello there here are {props.lines.length}
            {
                props.lines.map((line) => {
                    return (
                        <EditableLine key={line.id} sceneId={sceneId} lineId={line.id} />
                    );
                })
            }
        </div >
    )
}

const mapStateToProps = (state) => {
    return {
        lines: state.lines,
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        initialLoadLine: (sceneId) => {
            dispatch(initialLoadLine(sceneId));
        }
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(LinesContainer);