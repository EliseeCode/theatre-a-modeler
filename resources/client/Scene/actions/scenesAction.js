function getParams() {
    const token = $('.csrfToken').data('csrf-token');
    const params = {
        _csrf: token
    };
    return params;
}

export function getScenes(sceneId) {
    let params = { sceneId };
    return dispatch => {
        $.get('/play/getScenes/' + sceneId, function (data) {
            console.log("data from post", data);
            dispatch({
                type: "LOAD_SCENES",
                payload: data
            })
        })
    };
}

export function initialLoadSceneId(sceneId) {
    return {
        type: "LOAD_SCENEID",
        payload: sceneId
    };
}
