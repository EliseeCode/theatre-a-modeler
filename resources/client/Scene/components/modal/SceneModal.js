
import React, { useEffect, useState, useRef } from 'react'
import { connect } from "react-redux";
import { updateScene } from "../../actions/sceneAction";

const SceneModal = (props) => {

    const { showSceneModal, sceneId } = props;
    const [scene, setScene] = useState({ name: "Nouvelle scene", description: "" })
    const [selectedImage, setSelectedImage] = useState(null);
    const [previewImage, setPreviewImage] = useState(null);
    const csrfValue = $('.csrfToken').data('csrf-token');

    useEffect(() => {
        if (showSceneModal == 'new') {
            setScene({ name: "Nouvelle scene", description: "" })
        }
        else {
            let { name, description, image } = props.scenes.byIds[sceneId];
            setScene({ name, description });
            if (image) { console.log("image", image?.public_path); setPreviewImage(image?.public_path); }
        }
    }, [])
    useEffect(() => {
        if (selectedImage) {
            const objectUrl = URL.createObjectURL(selectedImage);
            setPreviewImage(objectUrl);
        }
    }, [selectedImage])

    function submitForm(event) {
        event.preventDefault();
        var form = new FormData(event.target);
        var data = Object.fromEntries(form);
        console.log(data);
        props.updateScene({ form: form, sceneId });
        props.closeSceneModal();
    }

    function handleSceneChange(event) {
        console.log(event.target.name, event.target.value);
        setScene({ ...scene, [event.target.name]: event.target.value });
    }

    function removeImage() {
        setSelectedImage(null);
        setPreviewImage(null);
    }

    function ChangeImage(event) {
        if (!event.target.files || event.target.files.length === 0) {
            setSelectedImage(null);
            return;
        }
        setSelectedImage(event.target.files[0]);
    }

    function chooseImage() {
        $("#imageSceneFile").click();
    }


    return (
        <div className="is-active modal" >
            <div className="modal-background" onClick={props.closeSceneModal}></div>
            <div className="modal-card">
                <header className="modal-card-head">
                    <p className="modal-card-title">{showSceneModal == 'new' ? 'Créer une scene' : 'Modifier la scene'}</p>
                    <button className="delete" aria-label="close" onClick={props.closeSceneModal}></button>
                </header>
                <section className="modal-card-body">
                    <form id="sceneForm" className="form" onSubmit={submitForm} action='/scenes' method='POST' encType="multipart/form-data">
                        <input type="hidden" name="_csrf" value={csrfValue} />
                        <input type="hidden" name="sceneId" value={props.sceneId} />
                        <input type="hidden" name="playId" value={props.playId} />

                        <div className="card-image p-3 has-text-centered">

                            <div className="sceneImageContainer">
                                {(selectedImage || previewImage) && (
                                    <div className="hasImage">
                                        <img className="imagePreviewNewScene" style={{ maxHeight: '200px' }} src={previewImage} alt="Placeholder image" />
                                        <button type="button" onClick={removeImage} className="delete deleteImage is-large is-danger" style={{ 'zIndex': 100, position: 'absolute' }}></button>
                                    </div>
                                )
                                }

                                <div className={`hasNoImage ${!selectedImage && "hidden"}`} >
                                    <input name="imageScene" id="imageSceneFile" onChange={ChangeImage} type="file" hidden />
                                    <label className="has-text-centered button btnUpload" onClick={chooseImage}>Choisir une image</label>
                                </div>



                            </div>


                        </div>
                        <div className="card-content">

                            <div className="field" style={{ color: "black" }}>
                                <div className="control">
                                    <input onInput={handleSceneChange} type="text" placeholder="Nom de la scene" className="input subtitle has-text-centered" name='name' value={scene.name} />
                                </div>
                            </div>
                        </div>




                    </form>
                </section>
                <footer className="modal-card-foot">
                    <button form='sceneForm' className="button is-primary" type="submit">Enregistrer</button>
                    <button className="button" type="button" onClick={props.closeSceneModal}>Annuler</button>
                </footer>
            </div>
        </div >

    )
}



const mapStateToProps = (state) => {
    return {
        scenes: state.scenes
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        updateScene: (data) => {
            dispatch(updateScene(data));
        },
    };
};




export default connect(mapStateToProps, mapDispatchToProps)(SceneModal);
