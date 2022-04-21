import React, { useState, useEffect, useRef } from 'react'
import { connect } from "react-redux"
import { useReactMediaRecorder } from "react-media-recorder";
import { removeAudio, uploadAudio } from "../actions/audiosAction"
import { selectLine, setLineAction } from "../actions/linesAction"
import Speech from 'speak-tts'

const Line = (props) => {
    const { selectedLineId, lineId, lines, characters, uploadAudio, audios, versions, userId } = props;

    const [line, setLine] = useState(lines.byIds[lineId]);
    const [character, setCharacter] = useState(null);
    const [selectedVersion, setSelectedVersion] = useState(character?.selectedAudioVersion);
    const audioElem = useRef();
    const [isPlaying, setIsPlaying] = useState(false);
    const [audioCreatorId, setAudioCreatorId] = useState("undefined");
    const [audioSrc, setAudioSrc] = useState(null);
    const [audioId, setAudioId] = useState(null);
    const speech = new Speech();
    const robotIsSupported = speech.hasBrowserSupport();
    useEffect(() => {
        setLine(lines.byIds[lineId]);
        setCharacter(characters.byIds[line.character_id]);
    }, [characters, lines]);

    useEffect(() => {
        setSelectedVersion(character?.selectedAudioVersion);
    }, [character])

    useEffect(() => {
        let audio = Object.values(audios.byIds).filter((audio) => { return (audio.line_id == lineId && audio.version_id == selectedVersion) });
        setAudioSrc(audio[0]?.public_path);
        setAudioId(audio[0]?.id);
        setAudioCreatorId(audio[0]?.creator_id);
    }, [selectedVersion, audios])

    useEffect(() => {
        if (lines.selectedId == lineId) {
            if (lines.action == 'play' && !isPlaying) {
                playPause();
            }
            else if (lines.action == 'pause' && isPlaying) {
                playPause();
            }
        }
    }, [lines])

    function onStop(blobUrl, Blob) {
        console.log(blobUrl, Blob);
        uploadAudio(lineId, selectedVersion, Blob);
        setAudioSrc(blobUrl);
    }

    function playPause() {
        if (!isPlaying) {
            (selectedVersion == -2 || !audioSrc) ? playRobot() : playAudio()
            props.setLineAction(lineId, "play");
        } else {
            (selectedVersion == -2 || !audioSrc) ? pauseRobot() : pauseAudio()
            props.setLineAction(lineId, "pause");
        }
        setIsPlaying(!isPlaying);
    }

    function playRobot() {
        if (robotIsSupported) { // returns a boolean
            console.log("speech synthesis supported")

            speech.init({
                'lang': 'fr-FR',
            }).then((data) => {
                speech.speak({
                    text: line.text,
                    queue: false,
                    listeners: {
                        onend: () => {
                            audioEnded();
                        }
                    }
                })
                    .then((data) => { })
                    .catch(e => { })
            })
        }
    }
    function pauseRobot() {
        speech.pause();
    }
    function playAudio() {
        audioElem.current.play();
    }
    function pauseAudio() {
        audioElem.current.pause();
    }
    function audioEnded() {
        setIsPlaying(false);
        props.setLineAction(lineId, "ended");
    }

    const {
        status,
        startRecording,
        stopRecording,
        mediaBlobUrl,
    } = useReactMediaRecorder({ audio: true, onStop, askPermissionOnMount: true });

    const lineStyle = { whiteSpace: 'pre-wrap' };
    const isActiveStyle = { boxShadow: '0 0 5px #c0c0c0', zIndex: 2 }
    return (
        <>
            <div className="levels p-3" onClick={() => { props.selectLine(lineId); }} style={selectedLineId == lineId ? isActiveStyle : {}}>
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
                        {((!selectedVersion || selectedVersion < 0) && robotIsSupported) && <button onClick={playPause} className="button"><span className="fas fa-robot"></span></button>}
                        {(userId != 'undefined' && !!selectedVersion) && (
                            status != 'recording' ?
                                <button onClick={startRecording} className="button"><span className="fas fa-microphone"></span></button>
                                : <button onClick={stopRecording} className="button" style={{ color: "red" }}><span className="fas fa-microphone"></span></button>
                        )}

                        {audioSrc && <button onClick={playPause} className="button"><span className={"fas " + (!isPlaying ? "fa-play" : "fa-pause")}></span></button>}
                        {(audioSrc && audioCreatorId == userId) && (<button onClick={() => { props.removeAudio(audioId) }} className="button is-danger ml-3"><span className="fas fa-trash"></span></button>)}
                        <audio ref={audioElem} onEnded={audioEnded} src={audioSrc} />
                    </div>
                </div>
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
        userId: state.miscellaneous?.user?.userId,
        selectedLineId: state.lines.selectedId
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
        selectLine: (lineId) => {
            dispatch(selectLine(lineId));
        },
        setLineAction: (lineId, action) => {
            dispatch(setLineAction(lineId, action))
        }
    };
};



export default connect(mapStateToProps, mapDispatchToProps)(Line);