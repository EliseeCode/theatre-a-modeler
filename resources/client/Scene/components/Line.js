import React, { useState, useEffect, useRef } from 'react'
import { connect } from "react-redux"
import { useReactMediaRecorder } from "react-media-recorder";
import { removeAudio, uploadAudio } from "../actions/audiosAction"

const Line = (props) => {
    const { lineId, lines, characters, uploadAudio, audios, versions, userId } = props;

    const line = lines.byIds[lineId];
    const character = characters.byIds[line.character_id];
    const selectedVersion = parseInt(character?.selectedAudioVersion || -1);
    const audioElem = useRef();
    const [isPlaying, setIsPlaying] = useState(false);
    const [audioCreatorId, setAudioCreatorId] = useState("undefined");
    const [audioSrc, setAudioSrc] = useState(null);
    const [audioId, setAudioId] = useState(null);

    useEffect(() => {
        let audio = Object.values(audios.byIds).filter((audio) => { return (audio.line_id == lineId && audio.version_id == selectedVersion) });
        setAudioSrc(audio[0]?.public_path);
        setAudioId(audio[0]?.id);
        setAudioCreatorId(audio[0]?.creator_id);

    }, [character, audios])

    function onStop(blobUrl, Blob) {
        console.log(blobUrl, Blob);
        uploadAudio(lineId, selectedVersion, Blob);
        setAudioSrc(blobUrl);
    }

    function playPause() {
        if (!isPlaying) {
            audioElem.current.play();
        } else {
            audioElem.current.pause();
        }
        setIsPlaying(!isPlaying);
    }

    const {
        status,
        startRecording,
        stopRecording,
        mediaBlobUrl,
    } = useReactMediaRecorder({ audio: true, onStop, askPermissionOnMount: true });

    const lineStyle = { whiteSpace: 'pre-wrap' };
    return (
        <>
            <div className="levels mb-3">
                <div className="level-item">
                    <div>
                        <div>
                            <i>{character?.name}</i>
                        </div>
                        <div style={lineStyle}>
                            {line.text}
                        </div>
                    </div>
                </div>

                <div className="level-right">
                    <div className="level-item">
                        {userId != 'undefined' && (
                            status != 'recording' ?
                                <button onClick={startRecording} className="button"><span className="fas fa-microphone"></span></button>
                                : <button onClick={stopRecording} className="button" style={{ color: "red" }}><span className="fas fa-microphone"></span></button>
                        )}

                        {audioSrc && <button onClick={playPause} className="button"><span className={"fas " + (!isPlaying ? "fa-play" : "fa-pause")}></span></button>}
                        {(audioSrc && audioCreatorId == userId) && (<button onClick={() => { props.removeAudio(audioId) }} className="button is-danger ml-3"><span className="fas fa-trash"></span></button>)}
                        <audio ref={audioElem} src={audioSrc} />
                    </div>
                </div>
                <hr />
            </div>
        </>
    )
}



const mapStateToProps = (state) => {
    return {
        lines: state.lines,
        characters: state.characters,
        audios: state.audios,
        versions: state.versions,
        userId: state.miscellaneous?.user?.userId
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        removeAudio: (lineId, versionId) => {
            dispatch(removeAudio(lineId, versionId));
        },
        uploadAudio: (lineId, versionId, blob) => {
            dispatch(uploadAudio(lineId, versionId, blob));
        },
    };
};



export default connect(mapStateToProps, mapDispatchToProps)(Line);