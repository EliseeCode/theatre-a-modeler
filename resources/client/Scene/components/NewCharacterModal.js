
import React, { useEffect, useState } from 'react'
import { connect } from "react-redux";
import { addCharacter } from "../actions/charactersAction";
import linesReducer from '../reducers/linesReducer';


const NewCharacterModal = (props) => {

    const [newCharacter, setNewCharacter] = useState({ name: "Bob", gender: "Male", description: "" })
    const [selectedImage, setSelectedImage] = useState(null);
    const [previewImage, setPreviewImage] = useState(null);
    const csrfValue = $('.csrfToken').data('csrf-token');

    useEffect(() => {
        if (!selectedImage) {
            setPreviewImage(null);
            return;
        }
        const objectUrl = URL.createObjectURL(selectedImage);
        setPreviewImage(objectUrl);

    }, [selectedImage])

    function submitForm(event) {
        event.preventDefault();
        var form = new FormData(event.target);
        var data = Object.fromEntries(form);
        props.addCharacter({ form: form, lineId: props.lineId });
        props.toggleModal();
    }

    function handleCharacterChange(event) {
        console.log(event.target.name, event.target.value);
        setNewCharacter({ ...newCharacter, [event.target.name]: event.target.value });
    }


    function removeImage() {
        setSelectedImage(null);
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
        <div className="is-active modal" id="create-newcharacter-modal" >
            <div className="modal-background" onClick={props.closeModal}></div>
            <div className="modal-card">
                <header className="modal-card-head">
                    <p className="modal-card-title">Créer un personnage</p>
                    <button className="delete" aria-label="close" onClick={props.toggleModal}></button>
                </header>
                <form onSubmit={submitForm} action='/characters' method='POST' encType="multipart/form-data">
                    <input type="hidden" name="_csrf" value={csrfValue} />
                    <input type="hidden" name="lineId" value={props.lineId} />
                    <section className="modal-card-body">
                        <div className="card-image p-3 has-text-centered">

                            <div className="characterImageContainer">
                                {selectedImage && (
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
                                    <input onInput={handleCharacterChange} type="text" placeholder="Nom du personnage" className="input subtitle has-text-centered" name='name' value={newCharacter.name} />
                                </div>
                            </div>
                            <div className="field">
                                <div className="control">
                                    <div className="select">
                                        <select name="gender" value={newCharacter.gender} onChange={handleCharacterChange}>

                                            <option value="Male" >Homme</option>
                                            <option value="Female" >Femme</option>
                                            <option value="Other">Autre</option>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div className="field">
                                <div className="control">
                                    <textarea onInput={handleCharacterChange} name="description" placeholder="Informations sur le personnage" id="" cols="30" rows="3" className="textarea" value={newCharacter.description}></textarea>
                                </div>
                            </div>




                        </div>


                    </section>
                    <footer className="modal-card-foot">
                        <button className="button is-primary" type="submit">Enregistrer</button>
                        <button className="button" type="button" onClick={props.toggleModal}>Annuler</button>
                    </footer>
                </form>
            </div>
        </div>

    )
}



const mapStateToProps = (state) => {
    return {};
};

const mapDispatchToProps = (dispatch) => {
    return {
        addCharacter: (data) => {
            dispatch(addCharacter(data));
        },
    };
};




export default connect(mapStateToProps, mapDispatchToProps)(NewCharacterModal);
