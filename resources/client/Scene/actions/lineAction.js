function getParams() {
    const token = $('.csrfToken').data('csrf-token');
    const params = {
        _csrf: token
    };
    return params;
}


export function updateText(text, lineId) {
    return {
        type: "UPDATE_TEXT",
        payload: { text, lineId }
    }
}

export function addLine(afterLinePos, sceneId) {
    let params = getParams();
    params = {
        ...params,
        afterLinePos,
        sceneId
    };

    return dispatch => {
        $.post('/line/create/', params, function (lines) {
            console.log(lines);
            dispatch({
                type: "ADD_LINE",
                payload: { lines, afterLinePos }
            })
        })
    }
}

export function deleteLine(lineId) {
    const params = getParams();

    return dispatch => {
        $.post('/line/' + lineId + '/destroy', params, function (lines) {
            return dispatch({
                type: "DELETE_LINE",
                payload: { lines }
            })
        })
    }
}

export function initialLoadLine(sceneId) {
    return dispatch => {
        $.get('/api/scene/' + sceneId + '/version/1/lines', function (data) {
            dispatch({
                type: "LOAD_LINES",
                payload: data
            });
            dispatch({
                type: "LOAD_CHARACTER",
                payload: data
            });
        })
    }
}
