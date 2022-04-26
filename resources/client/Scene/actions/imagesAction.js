export function initialLoadImages() {
    return dispatch => {
        $.get('/image/official/', function (data) {
            console.log("data from post", data);
            dispatch({
                type: "LOAD_IMAGES",
                payload: data
            })
        })
    };
}


