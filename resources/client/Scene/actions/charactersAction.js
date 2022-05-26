
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

export function updateCharacter(data) {
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
                    type: "UPDATE_CHARACTER",
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
export function removeCharacterAudioVersion(characterId, audioVersionId) {
    let params = getParams();
    params = {
        ...params,
        audioVersionId
    };
    return dispatch => {
        $.post('/character/removeAudioVersion', params, function (data) {
            console.log(data);
            dispatch({
                type: "REMOVE_CHARACTER_AUDIO_VERSION",
                payload: { characterId, audioVersionId }
            });
        })
    }
}
export function selectCharacterTextVersion(characterId, textVersionId) {

    return ({
        type: "SELECT_CHARACTER_TEXT_VERSION",
        payload: { characterId, textVersionId }
    })
}
export function removeCharacterTextVersion(characterId, textVersionId) {
    let params = getParams();
    params = {
        ...params,
        textVersionId
    };
    return dispatch => {
        $.post('/character/removeTextVersion', params, function (data) {
            console.log(data);
            dispatch({
                type: "REMOVE_CHARACTER_TEXT_VERSION",
                payload: { characterId, textVersionId }
            });
            dispatch({
                type: "SELECT_CHARACTER_TEXT_VERSION",
                payload: { characterId, textVersionId: 1 }
            });
        })
    }
}

export function createCharacterTextVersion(characterId, sceneId) {
    console.log(characterId, sceneId);
    let params = getParams();
    params = {
        ...params,
        characterId: characterId,
        sceneId: sceneId
    };

    return dispatch => {
        $.post('/character/createTextVersion', params, function (data) {
            console.log(data);
            dispatch({
                type: "CREATE_CHARACTER_TEXT_VERSION",
                payload: { characterId, ...data, sceneId }
            });
        })
    }

}

