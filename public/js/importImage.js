const CSRF_TOKEN = document.getElementById("csrfTokenElement").dataset.csrfToken;
const deleteButtons = Array.from(document.getElementsByClassName("deleteImage"));
const fileInputs = Array.from(document.querySelectorAll("[id^='file_']"));
fileInputs.map((elem) => {
    elem.addEventListener("change", (e) => {
        const fileInput = e.target;
        const entityType = fileInput.getAttribute("data-type")
        const entityId = fileInput.getAttribute("data-id");
        const file = fileInput.files[0];
        console.log("entityType" + entityType);
        console.log(`Here's the entity_id to upload: ${entityId}`);
        const form = new FormData();
        const reader = new FileReader();
        reader.addEventListener('load', event => {
            const buffer = event.target.result;
            const blob = new Blob([buffer], { type: file.type });
            form.append("image", blob);
            form.append("entityId", entityId);
            form.append("entityType", entityType);
            fetch(`${window.location.origin}/images`, {
                method: "POST",
                headers: {
                    'X-CSRF-Token': CSRF_TOKEN,
                    /* 'Accept': `${blob.type}`, // FIXME: Not working while file transfer?
                    'Content-Type': `${blob.type}`,*/
                    'Content-Transfer-Encoding': 'base64'
                },
                mode: "cors",
                body: form
            }).then(response => {
                if (!response.ok) throw response;
                return response.json();
            }).then((data) => {
                console.info(data);
                window.location.reload();
            }).catch((err) => {
                console.error(err);
            })
        });
        reader.readAsArrayBuffer(file);

    })
})
deleteButtons.map((elem) => {
    elem.addEventListener("click", (e) => {
        const id = e.target.getAttribute("data-image-id");
        fetch(`${window.location.origin}/images/${id}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-Token": CSRF_TOKEN
            }
        }).then(response => {
            if (!response.ok) throw response;
            return response.json();
        }).then((data) => {
            console.info(data);
            window.location.reload()
        }).catch((err) => {
            console.error(err);
        })
    })
})