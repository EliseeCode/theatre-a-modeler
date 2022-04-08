export function initialLoadAudios(sceneId) {
    let params = { sceneId };
    return dispatch => {
        $.get('/scene/getAudios/' + sceneId, function (data) {
            console.log("data from post", data);
            dispatch({
                type: "LOAD_AUDIO",
                payload: data
            })
        })
    };
}
