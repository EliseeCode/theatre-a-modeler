
import React, { useEffect, useState } from 'react'
import { connect } from "react-redux";
import { updateCharacter } from "../../actions/charactersAction";

const CharacterModal = (props) => {

    const { showCharacterModal, characterId } = props;
    const [character, setCharacter] = useState({ name: "Bob", gender: "Male", description: "" })
    const [selectedImage, setSelectedImage] = useState(null);
    const [previewImage, setPreviewImage] = useState(null);
    const csrfValue = $('.csrfToken').data('csrf-token');
    useEffect(() => {
        if (showCharacterModal == 'new') {
            setCharacter({ name: "Bob", gender: "Male", description: "" })
        }
        else {
            let { name, gender, description, image } = props.characters.byIds[characterId];
            setCharacter({ name, gender, description });
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
        props.updateCharacter({ form: form, lineId: props.lineId, characterId });
        props.closeCharacterModal();
    }

    function handleCharacterChange(event) {
        console.log(event.target.name, event.target.value);
        setCharacter({ ...character, [event.target.name]: event.target.value });
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
        $("#imageCharacterFile").click();
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
                        <input type="hidden" name="characterId" value={props.characterId} />

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
                                    <label className="has-text-centered button btnUpload" onClick={chooseImage}>Choisir une image</label>
                                </div>



                            </div>


                        </div>
                        <div className="card-content">

                            <div className="field">
                                <div className="control">
                                    <input onInput={handleCharacterChange} type="text" placeholder="Nom du personnage" className="input subtitle has-text-centered" name='name' value={character.name} />
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
        </div>

    )
}



const mapStateToProps = (state) => {
    return {};
};

const mapDispatchToProps = (dispatch) => {
    return {
        updateCharacter: (data) => {
            dispatch(updateCharacter(data));
        },
    };
};




export default connect(mapStateToProps, mapDispatchToProps)(CharacterModal);
