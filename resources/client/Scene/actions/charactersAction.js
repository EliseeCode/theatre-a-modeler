
function getParams() {
    const token = $('.csrfToken').data('csrf-token');
    const params = {
        _csrf: token
    };
    return params;
}

export function selectCharacter(characterId, lineId) {
    console.log(characterId, lineId);
    let params = getParams();
    params = {
        ...params,
        characterId: characterId,
        lineId: lineId
    };

    return dispatch => {
        $.post('/line/updateCharacter', params, function (data) {
            console.log(data);
            dispatch({
                type: "CHARACTER_SELECT",
                payload: { characterId, lineId }
            });
        })
    }
}

export function detachCharacter(characterId, sceneId) {
    console.log(characterId, sceneId);
    let params = getParams();
    params = {
        ...params,
        characterId: characterId,
        sceneId: sceneId
    };

    return dispatch => {
        $.post('/character/detach', params, function (data) {
            console.log(data);
            dispatch({
                type: "DETACH_CHARACTER",
                payload: { characterId, sceneId }
            });
        })
    }
}

export function addCharacter(data) {
    console.log(data);
    return dispatch => {
        $.ajax({
            url: '/characters',
            data: data.form,
            type: 'POST',
            processData: false,
            contentType: false,
            success: function (res) {
                let { character } = res;
                dispatch({
                    type: "ADD_CHARACTER",
                    payload: { character, lineId: data.lineId }
                })
            }
        });
    }
}

export function selectCharacterAudioVersion(characterId, audioVersionId) {
    return ({
        type: "SELECT_CHARACTER_AUDIO_VERSION",
        payload: { characterId, audioVersionId }
    })
}

