import React, { useEffect } from 'react'
import { removeAudio } from "../actions/audiosAction";
import { connect } from "react-redux";
import Line from "./Line";

const LinesShowContainer = (props) => {
    const { lines } = props;
    const constainerStyle = { marginBottom: "200px" }
    return (<>

        <div className="block" style={constainerStyle}>
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
        characters: state.characters
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