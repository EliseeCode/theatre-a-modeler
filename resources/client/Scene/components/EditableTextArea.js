import React, { useState, useEffect, useRef, useLayoutEffect } from 'react'
import { connect } from "react-redux"
import { updateText } from "../actions/linesAction";

const EditableTextArea = (props) => {
    const { lineId, lines } = props;
    const [line, setLine] = useState(lines.byIds[lineId]);
    const [isSaved, setIsSaved] = useState(false);
    //to autoSize the textarea
    //const [textareaHeight, setTextareaHeight] = useState('40px');
    const [initialRender, setInitRender] = useState(true);
    const textareaRef = useRef();
    //timer to save text change only 1s after last keydown
    const [timer, setTimer] = useState(null);
    const [text, setText] = useState(line.text);

    useLayoutEffect(() => {
        textareaRef.current.style.height = "inherit";
        textareaRef.current.style.height = `${Math.max(textareaRef.current.scrollHeight, 70)}px`;
        console.log(textareaRef);
    }, [text]);



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
            //setTextareaHeight(textareaRef.current.scrollHeight + "px");
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
        //setTextareaHeight(textareaRef.current.scrollHeight + "px");
    }, [text]);

    function handleChange(event) {
        var newText = event.target.value;
        setText(newText);
    }

    return (
        <>
            <div className={isSaved ? "saved" : ""} style={{ position: "relative" }}>
                <textarea ref={textareaRef} onInput={handleChange} value={text} className="lineText textarea" cols="60" rows="1"></textarea>
            </div>
        </>
    )
}



const mapStateToProps = (state) => {
    return {
        lines: state.lines,
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        updateText: (text, lineId) => {
            dispatch(updateText(text, lineId));
        },
    };
};



export default connect(mapStateToProps, mapDispatchToProps)(EditableTextArea);