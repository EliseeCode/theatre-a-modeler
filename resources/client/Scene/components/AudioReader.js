import React, { useState, useEffect } from 'react'
import { connect } from "react-redux"
import { selectPreviousLine, selectNextLine, selectLine, setLineAction, playNextLine } from "../actions/linesAction"
import { setAutoplay } from "../actions/audiosAction"

const AudioReader = (props) => {
    const { lines } = props;

    const [isAutoPlaying, setIsAutoPlaying] = useState(false);
    const [lineId, setLineId] = useState(null);

    useEffect(() => {
        if (lines.ids[0]) {
            !lines.selectedId && props.selectLine(lines.ids[0]);
        }
        //synchronize selectedLineId 
        lines.selectedId ? setLineId(lines.selectedId) : setLineId(lines.ids[0]);
    }, [lines])

    useEffect(() => { props.setAutoplay(isAutoPlaying) }, [isAutoPlaying])

    function selectPreviousLine() {
        props.selectPreviousLine(lineId, lines);
        setIsAutoPlaying(true);
    }
    function selectNextLine() {
        if (lines.ids.indexOf(lineId) != (lines.ids.length - 1)) {
            props.selectNextLine(lineId, lines);
            setIsAutoPlaying(true);
        }
    }
    function autoPlay() {
        props.setLineAction(lineId, "play");
        setIsAutoPlaying(true);
    }
    function autoPause() {
        props.setLineAction(lineId, "pause");
        setIsAutoPlaying(false);
    }

    const audioReaderStyle = {
        position: "fixed",
        backgroundColor: "",
        bottom: "20px",
        right: "20px",
        width: '200px',
        zIndex: 3,
        transform: 'scale(1.5)'
    }
    return (
        <div className="" style={audioReaderStyle}>
            <div className="levels mb-3">
                <div className="level-item">
                    <div className="" disabled={(lines.ids.indexOf(lineId) == 0)} onClick={selectPreviousLine}><span className="fas fa-step-backward"></span></div>
                    {!isAutoPlaying ? <button className="button is-rounded" onClick={autoPlay}><span className="fas fa-play"></span></button> :
                        <button className="button is-rounded" onClick={autoPause}><span className="fas fa-pause"></span></button>}
                    <div className="" disabled={(lines.ids.indexOf(lineId) == (lines.ids.length - 1))} onClick={selectNextLine}><span className="fas fa-step-forward"></span></div>
                </div>
            </div>
        </div>
    )
}



const mapStateToProps = (state) => {
    return {
        lines: state.lines,
        characters: state.characters,
        audios: state.audios,
        versions: state.versions,
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        selectLine: (lineId) => {
            dispatch(selectLine(lineId));
        },
        selectPreviousLine: (lineId, lines) => {
            dispatch(selectPreviousLine(lineId, lines));
        },
        selectNextLine: (lineId, lines) => {
            dispatch(selectNextLine(lineId, lines));
        },
        playNextLine: (lineId, lines) => {
            dispatch(playNextLine(lineId, lines));
        },
        setLineAction: (lineId, action) => {
            dispatch(setLineAction(lineId, action))
        },
        setAutoplay: (isAutoplay) => {
            dispatch(setAutoplay(isAutoplay))
        }
    };
};



export default connect(mapStateToProps, mapDispatchToProps)(AudioReader);