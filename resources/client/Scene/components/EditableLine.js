import React, { useState, useEffect, useRef } from 'react'
import CharacterSelect from './CharacterSelect'

import DeleteLineButton from './DeleteLineButton'
import LinesContainer from './LinesContainer';
import NewLineButton from './NewLineButton'

export default function EditableLine(props) {
    const [line, setLine] = useState(props.line);
    const [isSaved, setIsSaved] = useState(false);
    const [textareaHeight, setTextareaHeight] = useState('40px');
    const [initialRender, setInitRender] = useState(true);
    const textareaRef = useRef();
    const [timer, setTimer] = useState(null);

    useEffect(() => {
        setTextareaHeight(textareaRef.current.scrollHeight + "px");
        if (initialRender == true) { setInitRender(false); return; }

        const token = $('.csrfToken').data('csrf-token');
        const params = {
            line: line,
            _csrf: token
        };
        if (timer != null) {
            clearTimeout(timer);
            setTimer(null);
        }
        setTimer(setTimeout(() => {
            $.post('/line/updateText', params, function (data) {
                console.log("updateTextData")
                if (data) {
                    setIsSaved(true)
                    setTimeout(function () {
                        setIsSaved(false)
                    }, 500);
                }
            });
        }, 1000));

    }, [line]);

    function handleChange(event) {
        var text = event.target.value;
        setLine({ ...line, ['text']: text });
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
                    <CharacterSelect line={line} setLine={setLine} characterSelected={line.character} characters={props.characters} />
                    <div className="control">
                        {line.position == 0 ?? <NewLineButton setLines={props.setLines} afterPosition={line.position} />}
                        <div className={isSaved ? "saved" : ""}>
                            <textarea ref={textareaRef} onKeyDown={splitContent} onInput={handleChange} value={line.text} className="lineText textarea" style={textareaStyle} cols="30" rows="1"></textarea>
                        </div>
                        <NewLineButton setLines={props.setLines} afterPosition={props.line.position} />
                    </div>

                </div>
                <DeleteLineButton setLines={props.setLines} line={props.line} />
            </div>

        </>
    )
}
