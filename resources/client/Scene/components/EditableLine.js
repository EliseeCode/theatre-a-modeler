import React, { useState, useEffect, useRef } from 'react'
import CharacterSelect from './CharacterSelect'
import { connect } from "react-redux"
import NewLineButton from './NewLineButton'
import { deleteLine, addLine, updateText, splitContent } from "../actions/linesAction";

const EditableLine = (props) => {
    const { lineId, lines, characters } = props;

    const line = lines.byIds[lineId];

    const [isSaved, setIsSaved] = useState(false);
    //to autoSize the textarea
    const [textareaHeight, setTextareaHeight] = useState('40px');
    const [initialRender, setInitRender] = useState(true);
    const textareaRef = useRef();
    //timer to save text change only 1s after last keydown
    const [timer, setTimer] = useState(null);
    const [text, setText] = useState(line.text);


    useEffect(() => {
        if (isSaved) {
            setTimeout(function () {
                setIsSaved(false);
            }, 500);
        }
    }, [isSaved])

    //handle change in line text, resize the textarea and set a timer to save the new input after 1s if no new input.
    useEffect(() => {
        if (initialRender == true) {
            setTextareaHeight(textareaRef.current.scrollHeight + "px");
            setInitRender(false); return;
        }

        if (timer != null) {
            clearTimeout(timer);
            setTimer(null);
        }
        setTimer(setTimeout(() => {
            const token = $('.csrfToken').data('csrf-token');
            const params = {
                _csrf: token,
                text: text,
                lineId: lineId
            };
            $.post('/line/updateText', params, function (data) {
                setIsSaved(true);
            });
        }, 1000));

        props.updateText(text, lineId);
        setTextareaHeight(textareaRef.current.scrollHeight + "px");
    }, [text]);

    function handleChange(event) {
        var newText = event.target.value;
        setText(newText);
    }
    function checkForSplitText(event, lineId) {
        if (event?.ctrlKey && event?.keyCode === 13) {
            props.splitContent(event, lineId)
        }
    }
    const textareaStyle = { 'height': textareaHeight }

    return (
        <>
            <div className="field has-addons m-0">
                <CharacterSelect lineId={lineId} />
                <div className="control">

                    {line.position == 0 && <div style={{ height: 0 }}><NewLineButton addLine={props.addLine} sceneId={props.sceneId} afterLinePos={-1} /></div>}
                    <div className={isSaved ? "saved" : ""}>
                        <textarea ref={textareaRef} onKeyDown={(event) => { checkForSplitText(event, lineId) }} onInput={handleChange} value={text} className="lineText textarea" style={textareaStyle} cols="60" rows="1"></textarea>
                    </div>
                    <NewLineButton addLine={props.addLine} sceneId={props.sceneId} afterLinePos={line.position} />
                </div>
                <div className="control">
                    <button onClick={() => { props.deleteLine(line.id) }} className="button is-danger"><span className="icon fas fa-trash"></span></button>
                </div>
            </div>
        </>
    )
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
        deleteLine: (lineId) => {
            dispatch(deleteLine(lineId));
        },
        addLine: (afterLinePos, sceneId) => {
            dispatch(addLine(afterLinePos, sceneId));
        },
        updateText: (text, lineId) => {
            dispatch(updateText(text, lineId));
        },
        splitContent: (event, lineId) => {
            dispatch(splitContent(event, lineId));
        }
    };
};



export default connect(mapStateToProps, mapDispatchToProps)(EditableLine);