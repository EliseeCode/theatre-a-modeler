import React, { useState, useEffect, useRef } from 'react'
import CharacterSelect from './CharacterSelect'
import { connect } from "react-redux"
import NewLineButton from './NewLineButton'
import { deleteLine, addLine, updateText } from "../actions/lineAction";

const EditableLine = (props) => {
    const { lineId, lines, characters } = props;
    //states
    const line = lines.filter((line) => { return line.id == lineId })[0] || null;
    const [isSaved, setIsSaved] = useState(false);
    //to autoSize the textarea
    const [textareaHeight, setTextareaHeight] = useState('40px');
    const [initialRender, setInitRender] = useState(true);
    const textareaRef = useRef();
    //timer to save text change only 1s after last keydown
    const [timer, setTimer] = useState(null);


    useEffect(() => {
        if (isSaved) {
            setTimeout(function () {
                setIsSaved(false);
            }, 500);
        }
    },
        [isSaved])

    function handleChange(event) {
        var text = event.target.value;
        setTextareaHeight(textareaRef.current.scrollHeight + "px");
        if (initialRender == true) { setInitRender(false); return; }
        if (timer != null) {
            clearTimeout(timer);
            setTimer(null);
        }
        setTimer(setTimeout(() => {
            props.updateText(text, lineId);
            setIsSaved(true);
        }, 1000));
    }

    function splitContent(event) {
        if (event.ctrlKey && event.keyCode === 13) {
            var text = event.target.value;
            let curs = event.target.selectionStart;
            var firstPart = text.substr(0, curs);
            //setLine({ ...line, ['text']: firstPart })
            var secondPart = text.substr(curs);
            console.log(firstPart, secondPart);

            const token = $('.csrfToken').data('csrf-token');
            const params = {
                _csrf: token,
                firstPart,
                secondPart,
                prevLine: line,
            };

            $.post('/line/splitAText/', params, function (data) {
                console.log(data);
                props.setLines([]);
                props.setLines(data.lines);
            })
        }
    }

    const textareaStyle = { 'height': textareaHeight }

    return (
        <>
            <div className="field has-addons m-0">
                <div className="field has-addons m-0" >
                    <CharacterSelect line={line} characterSelected={line.character} characters={characters} />
                    <div className="control">
                        {line.position == 0 ?? <NewLineButton scene={scene} afterPosition={line.position} />}
                        <div className={isSaved ? "saved" : ""}>
                            <textarea ref={textareaRef} onKeyDown={splitContent} onInput={handleChange} value={line.text} className="lineText textarea" style={textareaStyle} cols="30" rows="1"></textarea>
                        </div>
                        <NewLineButton afterPosition={line.position} />
                    </div>

                </div>
                <button onClick={deleteLine} className="button is-danger"><span className="icon fas fa-trash"></span></button>
            </div>

        </>
    )
}



const mapStateToProps = (state) => {
    return {
        sceneId: state.scene.id,
        lines: state.lines,
        characters: state.characters
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        deleteLine: (lineId) => {
            dispatch(deleteLine(lineId));
        },
        addLine: (afterLinePos) => {
            dispatch(addLine(afterLinePos));
        },
        updateText: (text, lineId) => {
            dispatch(updateText(text, lineId));
        }
    };
};



export default connect(mapStateToProps, mapDispatchToProps)(EditableLine);