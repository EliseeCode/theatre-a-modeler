import React, { useState, useEffect, useRef } from 'react'
import { connect } from "react-redux"
import AudioButtons from './AudioButtons';
import { selectLine, setLineAction } from "../actions/linesAction"
import EditableTextArea from "./EditableTextArea"
const Line = (props) => {
    const { textVersions, selectedLineId, lineId, lines, characters, userId } = props;

    const [line, setLine] = useState(lines.byIds[lineId]);
    const [character, setCharacter] = useState(null);
    const [text, setText] = useState(line.text);
    useEffect(() => {
        setLine(lines.byIds[lineId]);
        setCharacter(characters.byIds[line.character_id]);
    }, [characters, lines]);

    const lineTextStyle = { whiteSpace: 'pre-wrap' };
    const lineStyle = { position: 'relative', padding: "10px 50px 10px 130px" };
    const characterImageStyle = { position: 'absolute', top: 0, left: 0, width: '100px', height: '100px', objectFit: 'contain' };
    const isActiveStyle = { boxShadow: '0 0 5px #c0c0c0', zIndex: 2, fontSize: '1.2em' }
    return (
        <>
            <div className="box levels" onClick={() => { props.selectLine(lineId); }} style={selectedLineId == lineId ? { ...lineStyle, ...isActiveStyle } : { ...lineStyle }}>
                {character?.image?.public_path && <img src={character?.image?.public_path} style={characterImageStyle} />}
                <div className="level-item">
                    <div>
                        <div>
                            <i>{character?.name}</i>
                        </div>
                        {(userId == textVersions.byIds[line.version_id].creator_id && line.version_id != 1) ?
                            <EditableTextArea lineId={lineId} lines={lines} /> :
                            (<div style={lineTextStyle}>
                                {text}
                            </div>)
                        }

                    </div>
                </div>

                <AudioButtons lineId={lineId} userId={userId} />

            </div>
        </>
    )
}



const mapStateToProps = (state) => {
    return {
        lines: state.lines,
        characters: state.characters,
        userId: state.miscellaneous?.user?.userId,
        selectedLineId: state.lines.selectedId,
        textVersions: state.textVersions
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        selectLine: (lineId) => {
            dispatch(selectLine(lineId));
        },
        setLineAction: (lineId, action) => {
            dispatch(setLineAction(lineId, action))
        }
    };
};



export default connect(mapStateToProps, mapDispatchToProps)(Line);