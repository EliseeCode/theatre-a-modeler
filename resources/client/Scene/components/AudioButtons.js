import React, { useState, useEffect, useRef } from 'react'
import { connect } from "react-redux"
import { useReactMediaRecorder } from "react-media-recorder";
import { removeAudio, uploadAudio } from "../actions/audiosAction"
import { selectLine, setLineAction } from "../actions/linesAction"
import Speech from 'speak-tts'

const AudioButtons = (props) => {

    const { lineId, userId, uploadAudio, audios, audioVersions, lines, characters } = props;
    const [line, setLine] = useState(lines.byIds[lineId]);
    const [character, setCharacter] = useState(null);
    const audioElem = useRef();
    const [isPlaying, setIsPlaying] = useState(false);
    const [audioCreatorId, setAudioCreatorId] = useState("undefined");
    const [audioSrc, setAudioSrc] = useState(null);
    const [audioId, setAudioId] = useState(null);
    const [selectedAudioVersion, setSelectedAudioVersion] = useState(characters.byIds[lines.byIds[lineId]?.character_id]?.selectedAudioVersion || -1);
    const speech = new Speech();
    const robotIsSupported = speech.hasBrowserSupport();

    useEffect(() => {
        let audio = Object.values(audios.byIds).filter((audio) => { return (audio.line_id == lineId && audio.version_id == selectedAudioVersion) });
        setAudioSrc(audio[0]?.public_path);
        setAudioId(audio[0]?.id);
        setAudioCreatorId(audio[0]?.creator_id);
    }, [selectedAudioVersion, audios])

    useEffect(() => {
        setSelectedAudioVersion(character?.selectedAudioVersion || -1);
    }, [character, audios])

    useEffect(() => {
        setLine(lines.byIds[lineId]);
        setCharacter(characters.byIds[line.character_id]);
    }, [characters, lines]);

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
        uploadAudio(lineId, selectedAudioVersion, Blob);
        setAudioSrc(blobUrl);
    }

    function playPause() {
        if (!isPlaying) {
            (selectedAudioVersion == -2 || !audioSrc) ? playRobot() : playAudio()
            props.setLineAction(lineId, "play");
        } else {
            (selectedAudioVersion == -2 || !audioSrc) ? pauseRobot() : pauseAudio()
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


    return (
        <div className="level-right">
            <div className="level-item">
                {((!selectedAudioVersion || selectedAudioVersion < 0) && robotIsSupported) && <button onClick={playPause} className="button"><span className="fas fa-robot"></span></button>}
                {(userId != 'undefined') && (
                    status != 'recording' ?
                        <button onClick={startRecording} className="button"><span className="fas fa-microphone"></span></button>
                        : <button onClick={stopRecording} className="button" style={{ color: "red" }}><span className="fas fa-microphone"></span></button>
                )}
                {audioSrc && <button onClick={playPause} className="button"><span className={"fas " + (!isPlaying ? "fa-play" : "fa-pause")}></span></button>}
                {(audioSrc && audioCreatorId == userId) && (<button onClick={() => { props.removeAudio(audioId) }} className="button is-danger ml-3"><span className="fas fa-trash"></span></button>)}
                <audio ref={audioElem} onEnded={audioEnded} src={audioSrc} />
            </div>
        </div>
    )
}

const mapStateToProps = (state) => {
    return {
        lines: state.lines,
        characters: state.characters,
        audios: state.audios,
        audioVersions: state.audioVersions,
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

export default connect(mapStateToProps, mapDispatchToProps)(AudioButtons);