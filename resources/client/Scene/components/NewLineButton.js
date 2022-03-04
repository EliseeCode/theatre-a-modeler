import React from 'react'
import { useParams } from 'react-router-dom';

export default function NewLineButton(props) {
    const { sceneId } = useParams();
    function NewLineHandler(afterPosition) {
        const token = $('.csrfToken').data('csrf-token');
        const params = {
            _csrf: token
        };
        $.post('/api/scenes/' + sceneId + '/line/create/' + afterPosition, params, function (data) {
            console.log(data);
            props.setLines(data.lines);
        })
    }
    return (
        <div className="field buttonAddLine buttonAddLineFirst">
            <button onClick={() => NewLineHandler(props.afterPosition)} className="fas fa-plus" type="submit" title="Ajouter une réplique ici"></button>
        </div>
    )
}
