
import React, { useState } from 'react'

export default function NewCharacterModal(props) {

    const [newCharacter, setNewCharacter] = useState({ name: "", gender: "Male", description: "" })
    const csrfValue = $('.csrfToken').data('csrf-token');
    function handleCharacterChange(event) {
        console.log(event.target.name, event.target.value);
        setNewCharacter({ ...newCharacter, [event.target.name]: event.target.value });
    }


    function removeImage() {

    }
    function previewImage() {

    }
    const setCharacterSelected = props.setCharacterSelected;
    return (
        <div className={(props.showCharacterModal && "is-active ") + " modal"} id="create-newcharacter-modal" >
            <div className="modal-background" onClick={props.closeModal}></div>
            <div className="modal-card">
                <header className="modal-card-head">
                    <p className="modal-card-title">Créer un personnage</p>
                    <button className="delete" aria-label="close" onClick={props.closeModal}></button>
                </header>
                <form action='/characters' method='POST' encType="multipart/form-data">
                    <input type="hidden" name="_csrf" value={csrfValue} />
                    <input type="hidden" name="sceneId" value={props.line.scene_id} />
                    <input type="hidden" name="lineId" value={props.line.id} />
                    <section className="modal-card-body">
                        <div className="card-image p-3 has-text-centered">

                            <div className="characterImageContainer">
                                <div className="hasImage" style={{ display: 'none', position: 'relative' }}>
                                    <img className="imagePreviewNewCharacter" src="" alt="Placeholder image" />
                                    <button type="button" onClick={removeImage} className="delete deleteImage is-large is-danger" style={{ 'zIndex': 100, position: 'absolute' }}></button>
                                </div>
                                <div className="hasNoImage">
                                    <input name="imageCharacter" id="imageCharacterFile" onChange={previewImage} type="file" hidden />
                                    <label className="has-text-centered button btnUpload">Choisir une image</label>
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
                        <button className="button is-primary" type="submit" >Enregistrer</button>
                        <button className="button" type="button" onClick={props.closeModal}>Annuler</button>
                    </footer>
                </form>
            </div>
        </div>

    )
}
