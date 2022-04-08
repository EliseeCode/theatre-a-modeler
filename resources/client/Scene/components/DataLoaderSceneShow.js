import React, { useEffect } from 'react'
import { useParams } from 'react-router';
import { initialLoadLines } from "../actions/linesAction";
import { initialLoadSceneId } from "../actions/sceneAction";
import { initialLoadAudios } from "../actions/audiosAction";
import { connect } from "react-redux";

const DataLoaderSceneShow = (props) => {

    const { sceneId } = useParams();

    useEffect(() => {
        props.initialLoadSceneId(sceneId);
        props.initialLoadLines(sceneId);
        props.initialLoadAudios(sceneId);
        //props.initialLoadVoiceActor(sceneId);
    }, [])


    return (<></>)
}

const mapStateToProps = (state) => {
    return {};
};

const mapDispatchToProps = (dispatch) => {
    return {
        initialLoadLines: (sceneId) => {
            dispatch(initialLoadLines(sceneId));
        },
        initialLoadSceneId: (sceneId) => {
            dispatch(initialLoadSceneId(sceneId));
        },
        initialLoadAudios: (sceneId) => {
            dispatch(initialLoadAudios(sceneId));
        }
    };
};




export default connect(mapStateToProps, mapDispatchToProps)(DataLoaderSceneShow);