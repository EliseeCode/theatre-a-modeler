import React, { useState, useEffect } from 'react'
import { useParams } from 'react-router-dom'
import { connect } from "react-redux"
import { getPlay } from "../actions/playAction"
import { getScenes } from "../actions/scenesAction"
import SceneModal from './modal/SceneModal'

const PlaySceneSelector = (props) => {


    const { sceneId } = useParams();
    const { scenes, play, lines, characters, editContext } = props;
    const sceneCreatorId = scenes.byIds[sceneId]?.creator_id;
    const [showSceneModal, setShowSceneModal] = useState(false);
    useEffect(() => {
        props.getScenes(sceneId);
        props.getPlay(sceneId);
    }, [])

    function closeSceneModal() {
        setShowSceneModal(false);
    }

    function openUpdateSceneModal() {
        setShowSceneModal("update");
    }
    const coverStyle = {
        backgroundImage: `url(${scenes.byIds[sceneId]?.image?.public_path || '/img/fondTheatre.png'})`,
        backgroundPosition: "center center",
        backgroundSize: "cover",
        minHeight: '500px',
        padding: '50px',
        paddingBottom: '200px',
        paddingTop: '50px'
    }

    return (<>
        <div className="hero" style={coverStyle}>
            {sceneCreatorId == props.userId && (<div className="level-right">
                <div className="level-item">
                    <div onClick={() => { setShowSceneModal("update") }}
                        className="button is-small icon-text"
                        style={{ color: "black" }}>
                        <span className="icon fas fa-edit" >
                        </span>
                        <span>
                            Modifier la couverture
                        </span>
                    </div>
                </div>
            </div>)}
        </div>
        <div className="box container" style={{
            marginTop: "-100px", borderRadius: '20px 20px 0 0',
            overflow: 'hidden'
        }}>
            <h1 className="title">{play.name}</h1>
            <div className="level">
                <div className="level-item">
                    <h2 className="subtitle">
                        <div className="select">
                            <select name="scene" value={sceneId} id="" onChange={(e) => { window.location.href = '/scene/' + e.target.value; }}>
                                {scenes?.ids.map((id, index) => {
                                    return (<option key={index} value={id}>{scenes.byIds[id].name}</option>)
                                })
                                }
                            </select>
                        </div>
                    </h2>
                </div>
            </div>
            {editContext ?
                (<>
                    <div className="level-right">
                        <div className="level-item">
                            <a href={`/scene/${sceneId}`}
                                className="button is-small icon-text"
                                style={{ color: "black" }}>
                                <span className="icon fas fa-eye" >
                                </span>
                                <span>
                                    Visualiser la scene
                                </span>
                            </a>
                        </div>
                    </div>
                </>) :
                sceneCreatorId == props.userId && (
                    <div className="level-right">
                        <div className="level-item">
                            <a href={`/scene/${sceneId}/edit`}
                                className="button is-small icon-text"
                                style={{ color: "black" }}>
                                <span className="icon fas fa-edit" >
                                </span>
                                <span>
                                    Editer la scene
                                </span>
                            </a>
                        </div>
                    </div>)}
            {
                showSceneModal && <SceneModal
                    closeSceneModal={closeSceneModal}
                    sceneId={sceneId}
                    playId={play.id}
                    showSceneModal={showSceneModal}
                />
            }
        </div >
    </>
    )
}


const mapStateToProps = (state) => {
    console.log(state.miscellaneous)
    return {
        play: state.play,
        scenes: state.scenes,
        lines: state.lines,
        characters: state.characters,
        userId: state.miscellaneous.user?.userId
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        getScenes: (sceneId) => {
            dispatch(getScenes(sceneId));
        },
        getPlay: (sceneId) => {
            dispatch(getPlay(sceneId));
        },
    };
};



export default connect(mapStateToProps, mapDispatchToProps)(PlaySceneSelector);