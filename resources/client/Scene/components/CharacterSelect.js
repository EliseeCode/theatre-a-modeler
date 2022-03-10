
import React, { useState } from 'react'
import NewCharacterModal from './NewCharacterModal';

export default function CharacterSelect(props) {
    const [isDropdownActive, setDropdownActive] = useState(false);
    const [characterSelected, setCharacterSelected] = useState(props.characterSelected);

    const [showCharacterModal, setShowCharacterModal] = useState(false);
    function toggleModal() {
        setShowCharacterModal(!showCharacterModal);
    }
    return (

        <div className="control">

            <div className={(isDropdownActive ? "is-active" : "") + ' dropdown'} style={{ height: "38px" }}>
                <div onClick={() => setDropdownActive(!isDropdownActive)} className="dropdown-trigger button" aria-haspopup="true" aria-controls="dropdown-menu" style={{ width: '300px' }}>
                    <div className="level" style={{ width: '300px' }}>
                        <div className="level-left">
                            <div className="level-item">
                                {characterSelected?.image ?? <img className="image-character" src={characterSelected?.image?.publicPath} />}
                            </div>
                        </div>
                        <div className="level-item">
                            {characterSelected?.name || 'Choisir un personnage'}
                        </div>
                        <div className="level-right">
                            <div className="level-item">
                                <i className="fas fa-angle-down" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div className='dropdown-menu' id="dropdown-menu" role="menu">
                    <div className="dropdown-content">
                        {
                            props.characters.map((character, index) => {
                                return (
                                    <div key={index} className="dropdown-item">
                                        {character?.image ?? <img className="image-character" src={character?.image?.publicPath} />}
                                        {character.name}
                                    </div>
                                );
                            })
                        }
                        <div className="dropdown-item" onClick={toggleModal}>Nouveau personnage</div>
                    </div>


                </div>
            </div>
            {showCharacterModal && <NewCharacterModal
                closeModal={toggleModal}
                showCharacterModal={showCharacterModal}
                setCharacterSelected={setCharacterSelected}
                setLine={props.setLine}
                line={props.line}
            />}
        </div>
    )
}
