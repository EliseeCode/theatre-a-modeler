import React, { useEffect } from 'react'
import { initialLoadLines } from "../actions/linesAction";
import { connect } from "react-redux";

const LinesShowContainer = (props) => {
    const { lines, audios } = props;
    return (
        <div className="box block">

            {
                lines.ids.map((lineId) => {
                    return (<div key={lineId}>
                        {lines.byIds[lineId].text}
                    </div>)
                })
            }
            {
                JSON.stringify(audios)
                // audios.ids.map((audioId) => {
                //     return (<div key={audioId}>
                //         {audios.byIds[audioId].id}
                //     </div>)
                // })
            }
        </div >
    )
}

const mapStateToProps = (state) => {
    return {
        lines: state.lines,
        characters: state.characters,
        audios: state.audios
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        // initialLoadLines: (sceneId) => {
        //     dispatch(initialLoadLines(sceneId));
        // }
    };
};




export default connect(mapStateToProps, mapDispatchToProps)(LinesShowContainer);