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
    console.log(afterLinePos, sceneId);
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
    let params = getParams();

    return dispatch => {
        $.post('/line/' + lineId + '/destroy', params, function (lines) {
            return dispatch({
                type: "DELETE_LINE",
                payload: { lines }
            })
        })
    }
}

export function initialLoadOfficialLines(sceneId) {
    return dispatch => {
        $.get('/scene/' + sceneId + '/version/1/lines', function (data) {
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

export function initialLoadLines(sceneId) {
    console.log("initialLoadLines");
    return dispatch => {
        $.get('/scene/' + sceneId + '/lines', function (data) {
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

export function splitContent(event, lineId) {
    var text = event.target.value;
    let curs = event.target.selectionStart;
    var firstPart = text.substr(0, curs);
    var secondPart = text.substr(curs);
    let params = getParams();
    params = {
        ...params,
        firstPart,
        secondPart,
        lineId: lineId,
    };
    return dispatch => {
        $.post('/line/splitAText', params, function (data) {
            console.log("data from post", data);
            dispatch({
                type: "SPLIT_LINE",
                payload: { ...params, newLine: data.newLine }
            })
        })
    }

}
