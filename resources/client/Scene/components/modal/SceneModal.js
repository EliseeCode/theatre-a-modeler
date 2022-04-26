
import React, { useEffect, useState, useRef } from 'react'
import { connect } from "react-redux";
import { updateScene } from "../../actions/sceneAction";

const SceneModal = (props) => {

    const { showSceneModal, sceneId, images } = props;
    const [scene, setScene] = useState({ name: "Nouvelle scene", description: "" })
    const [selectedImage, setSelectedImage] = useState(null);
    const [previewImage, setPreviewImage] = useState(null);
    const [selectedOfficialImage, setSelectedOfficialImage] = useState(null);
    const [imageSelectorOpen, setImageSelectorOpen] = useState(true);
    const csrfValue = $('.csrfToken').data('csrf-token');

    useEffect(() => {
        if (showSceneModal == 'new') {
            setScene({ name: "Nouvelle scene", description: "" })
        }
        else {
            let { name, description, image } = props.scenes.byIds[sceneId];
            setScene({ name, description });
            if (image) {
                console.log("image", image?.public_path);
                setPreviewImage(image?.public_path);
                if (images.map((img) => { return img.id; }).includes(image.id)) {
                    setSelectedOfficialImage(image.id);
                }
            }
        }
    }, [])
    useEffect(() => {
        if (selectedImage) {
            const objectUrl = URL.createObjectURL(selectedImage);
            setPreviewImage(objectUrl);
        }
    }, [selectedImage])

    useEffect(() => {
        if (selectedOfficialImage) {
            let image = images.filter((image) => { return image.id == selectedOfficialImage })[0];
            if (image) {
                setPreviewImage(image.public_path);
            }
        }
    }, [selectedOfficialImage])

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
        setSelectedOfficialImage(null);
    }

    function ChangeImage(event) {
        if (!event.target.files || event.target.files.length === 0) {
            setSelectedImage(null);
            return;
        }
        setSelectedImage(event.target.files[0]);
        setSelectedOfficialImage(null);
    }

    function chooseImage() {
        $("#imageSceneFile").click();
    }

    function toggleSelectedOfficialImage(imageId) {
        if (selectedOfficialImage != imageId) { setSelectedOfficialImage(imageId); }
        else {
            setPreviewImage(null); setSelectedOfficialImage(null);
        }
    }
    const imageOfficialStyle = {
        width: "160px",
        height: "80px",
        objectFit: "cover",
        borderRadius: "5px",
    }
    const SelectorImageofficialItem = {
        margin: "10px",
        padding: "5px",
        border: "1px grey solid",
        borderRadius: "8px",
        display: "inline-block"
    }
    const SelectorImageofficialItemActive = {
        margin: "10px",
        padding: "4px",
        border: "2px lime solid",
        borderRadius: "8px",
        display: "inline-block"
    }
    const officialImageContainer = {
        width: "100%",
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
                        <input type="hidden" name="officialImageId" value={selectedOfficialImage || ""} />

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
                                    <input name="imageCharacter" id="imageCharacterFile" onChange={ChangeImage} type="file" hidden />
                                    {imageSelectorOpen ? (<div>
                                        <h2 className="subtitle">Choix de l'image</h2>
                                        <label htmlFor="imageCharacter" className="has-text-centered button btnUpload" onClick={chooseImage}>Depuis l'ordinateur</label>
                                        <div style={officialImageContainer}>
                                            {images.map((image, index) => {
                                                return (
                                                    <div key={index} onClick={() => { toggleSelectedOfficialImage(image.id) }} style={selectedOfficialImage == image.id ? SelectorImageofficialItemActive : SelectorImageofficialItem}>
                                                        <img src={image.public_path} style={imageOfficialStyle} />
                                                    </div>)
                                            })}
                                        </div>
                                    </div>
                                    ) : (
                                        <button className="has-text-centered button btnUpload" onClick={() => { setImageSelectorOpen(true) }}>Choisir une image</button>
                                    )}


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
        scenes: state.scenes,
        images: state.images.coverImages
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
