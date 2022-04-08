

export function initialLoadSceneId(sceneId) {
    return {
        type: "LOAD_SCENEID",
        payload: sceneId
    };
}
// export function initialLoadScene(sceneId) {
//     return dispatch => {
//         $.post('/line/splitAText', params, function (data) {
//             console.log("data from post", data);
//             dispatch({
//                 type: "LOAD_SCENE",
//                 payload: { ...params, newLine: data.newLine }
//             })
//         })
//     };
// }
