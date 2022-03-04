import React from 'react'
import CharacterSelect from './CharacterSelect'

import DeleteLineButton from './DeleteLineButton'
import NewLineButton from './NewLineButton'

export default function EditableLine(props) {

    return (
        <>
            <div className="field has-addons m-0">
                <div className="field has-addons m-0" >

                    <CharacterSelect characterSelected={props.line.character} characters={props.characters} />
                    <div className="control">
                        <div>
                            <textarea value={props.line.text} className="lineText textarea"></textarea>
                            {/* onkeydown="splitContent(event,this);"
                                oninput="auto_grow(this);updateText();"
                                name='text' id="lineText_"
                                className="lineText textarea"
                                cols="30"
                                rows="1"
                                style="resize: none">test</textarea> */}

                        </div>
                    </div>
                </div>
                <DeleteLineButton lineId={props.line.id} />
            </div>
            <NewLineButton afterPosition={props.line.position} />
        </>
    )
}
