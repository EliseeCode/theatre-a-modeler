function getParams() {
    const token = $('.csrfToken').data('csrf-token');
    const params = {
        _csrf: token
    };
    return params;
}


export function updateText(text, lineId) {
    let params = getParams();
    params = {
        ...params,
        text: text,
        lineId: lineId
    };
    $.post('/line/updateText', params, function (data) {
        console.log("updateTextData")
        return {
            type: "UPDATE_TEXT",
            payload: line
        }
    });

}

export function addLine(afterLineId) {
    const params = getParams();
    $.post('/api/line/create/' + afterLineId, params, function (data) {
        console.log(data);
        return {
            type: "ADD_LINE",
            payload: data
        }
    })
}

export function deleteLine(lineId) {
    const params = getParams();
    $.post('/api/line/' + lineId + '/destroy', params, function (data) {
        console.log(data);
        return {
            type: "DELETE_LINE",
            payload: { lineId }
        }
    })
}

export function initialLoadLine(sceneId) {
    return dispatch => {
        $.get('/api/scene/' + sceneId + '/version/1/lines', function (data) {
            dispatch({
                type: "LOAD_LINE",
                payload: data
            });
        })
    }
}
