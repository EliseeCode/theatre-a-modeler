import React from 'react'

export default function DeleteLineButton(props) {
    function DeleteLineHandler() {
        const token = $('.csrfToken').data('csrf-token');
        const params = {
            _csrf: token
        };
        $.post('/api/line/' + props.line.id + '/destroy', params, function (data) {
            console.log(data);
            props.setLines(data.lines);
        })
    }
    return (
        <button onClick={DeleteLineHandler} className="button is-danger"><span className="icon fas fa-trash"></span></button>
    )
}
