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
                type: "LOAD_CHARACTERS",
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
                type: "LOAD_TEXT_VERSIONS",
                payload: data
            });
            dispatch({
                type: "LOAD_LINES",
                payload: data
            });
            dispatch({
                type: "LOAD_CHARACTERS",
                payload: data
            });
        })
    }
}


export function selectPreviousLine(lineId, lines) {
    const linePos = lines.ids.indexOf(lineId) == -1 ? 0 : lines.ids.indexOf(lineId);
    const newLineId = lines.ids[(linePos - 1 + lines.ids.length) % lines.ids.length];
    return {
        type: "SELECT_LINE",
        payload: { lineId: newLineId }
    }
}
export function selectNextLine(lineId, lines) {
    const linePos = lines.ids.indexOf(lineId) == -1 ? 0 : lines.ids.indexOf(lineId);
    const newLineId = lines.ids[(linePos + 1) % lines.ids.length];
    return {
        type: "SELECT_LINE",
        payload: { lineId: newLineId }
    }
}
export function playNextLine(lineId, lines) {
    const linePos = lines.ids.indexOf(lineId) == -1 ? 0 : lines.ids.indexOf(lineId);
    const newLineId = lines.ids[(linePos + 1) % lines.ids.length];
    return {
        type: "PLAY_LINE",
        payload: { lineId: newLineId }
    }
}

export function selectLine(lineId) {
    return {
        type: "SELECT_LINE",
        payload: { lineId }
    }
}
export function setLineAction(lineId, action) {
    return {
        type: "SET_LINE_ACTION",
        payload: { lineId, action }
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
                payload: { ...params, newLine: data.newLine, lineId }
            })
        })
    }

}
