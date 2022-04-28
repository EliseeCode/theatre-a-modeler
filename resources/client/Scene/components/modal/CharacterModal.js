
import React, { useEffect, useState } from 'react'
import { connect } from "react-redux";
import { updateCharacter } from "../../actions/charactersAction";

const CharacterModal = (props) => {

    const { showCharacterModal, characterId, images } = props;
    const [character, setCharacter] = useState({ name: "Bob", gender: "Male", description: "" })
    const [selectedImage, setSelectedImage] = useState(null);
    const [selectedOfficialImage, setSelectedOfficialImage] = useState(null);
    const [imageSelectorOpen, setImageSelectorOpen] = useState(true);
    const [previewImage, setPreviewImage] = useState(null);

    const csrfValue = $('.csrfToken').data('csrf-token');
    useEffect(() => {
        console.log("showCharacterModal", showCharacterModal);
        if (showCharacterModal == 'new') {
            setCharacter({ name: "Bob", gender: "Male", description: "" })
        }
        else if (showCharacterModal == 'update') {
            let { name, gender, description, image } = props.characters.byIds[characterId];
            setCharacter({ name, gender, description });
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
        props.updateCharacter({ form: form, lineId: props.lineId, characterId });
        props.closeCharacterModal();
    }

    function handleCharacterChange(event) {
        console.log(event.target?.name, event.target?.value);
        setCharacter({ ...character, [event.target?.name]: event.target.value });
    }

    function removeImage() {
        setSelectedImage(null);
        setPreviewImage(null);
        setSelectedOfficialImage(null);
    }

    function ChangeImage(event) {
        if (!event.target.files || event.target.files.length === 0) {
            setSelectedImage(null);
            setSelectedOfficialImage(null);
            return;
        }
        setSelectedImage(event.target.files[0]);
        setSelectedOfficialImage(null);
    }

    function chooseImage() {
        $("#imageCharacterFile").click();
    }

    function toggleSelectedOfficialImage(imageId) {
        if (selectedOfficialImage != imageId) { setSelectedOfficialImage(imageId); }
        else {
            setPreviewImage(null); setSelectedOfficialImage(null);
        }
    }

    const imageOfficialStyle = {
        width: "80px",
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
            <div className="modal-background" onClick={props.closeCharacterModal}></div>
            <div className="modal-card">
                <header className="modal-card-head">
                    <p className="modal-card-title">{showCharacterModal == 'new' ? 'Créer un personnage' : 'Modifier le personnage'}</p>
                    <button className="delete" aria-label="close" onClick={props.closeCharacterModal}></button>
                </header>
                <section className="modal-card-body">
                    <form id="characterForm" className="form" onSubmit={submitForm} action='/characters' method='POST' encType="multipart/form-data">
                        <input type="hidden" name="_csrf" value={csrfValue} />
                        <input type="hidden" name="lineId" value={props.lineId} />
                        <input type="hidden" name="characterId" value={props.characterId || ""} />
                        <input type="hidden" name="officialImageId" value={selectedOfficialImage || ""} />
                        <input type="hidden" name="action" value={props.showCharacterModal || ""} />

                        <div className="card-image p-3 has-text-centered">

                            <div className="characterImageContainer">
                                {(selectedImage || previewImage) && (
                                    <div className="hasImage">
                                        <img className="imagePreviewNewCharacter" src={previewImage} alt="Placeholder image" />
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

                            <div className="field">
                                <div className="control">
                                    <input onInput={handleCharacterChange} type="text" placeholder="Nom du personnage" className="input subtitle has-text-centered" name='name' value={character?.name} />
                                </div>
                            </div>
                        </div>
                    </form>
                </section>
                <footer className="modal-card-foot">
                    <button form='characterForm' className="button is-primary" type="submit">Enregistrer</button>
                    <button className="button" type="button" onClick={props.closeCharacterModal}>Annuler</button>
                </footer>
            </div>
        </div >

    )
}



const mapStateToProps = (state) => {
    return {
        images: state.images.characterImages
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        updateCharacter: (data) => {
            dispatch(updateCharacter(data));
        },
    };
};




export default connect(mapStateToProps, mapDispatchToProps)(CharacterModal);
