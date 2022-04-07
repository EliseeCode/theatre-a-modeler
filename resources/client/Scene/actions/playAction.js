export function getPlay(sceneId) {
    let params = { sceneId };
    return dispatch => {
        $.get('/scene/getPlay/' + sceneId, function (data) {
            console.log("data from post", data);
            dispatch({
                type: "LOAD_PLAY",
                payload: data
            })
        })
    };
}
