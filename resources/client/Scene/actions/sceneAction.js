

export function initialLoadSceneId(sceneId) {
    return {
        type: "LOAD_SCENEID",
        payload: sceneId
    };
}

export function updateScene(data) {
    console.log('data', data);
    return dispatch => {
        $.ajax({
            url: '/scene/update',
            data: data.form,
            type: 'POST',
            processData: false,
            contentType: false,
            success: function (res) {
                let { scene } = res;
                dispatch({
                    type: "UPDATE_SCENE",
                    payload: { scene }
                })
            }
        });
    }
}
