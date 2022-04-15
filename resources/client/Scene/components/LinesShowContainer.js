import React, { useEffect } from 'react'
import { removeAudio } from "../actions/audiosAction";
import { connect } from "react-redux";
import Line from "./Line";

const LinesShowContainer = (props) => {
    const { lines, audios } = props;

    return (<>

        <div className="box block">
            {
                lines.ids.map((lineId) => {
                    return (<Line key={lineId} lineId={lineId} />)
                })
            }
        </div >

    </>
    )
}

const mapStateToProps = (state) => {
    return {
        lines: state.lines,
        characters: state.characters,
        audios: state.audios
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        removeAudio: (audioId) => {
            dispatch(removeAudio(audioId));
        }
    };
};




export default connect(mapStateToProps, mapDispatchToProps)(LinesShowContainer);