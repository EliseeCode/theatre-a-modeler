
import React, { useState, useRef, useEffect } from 'react'
import NewCharacterModal from './NewCharacterModal';
import { selectCharacter } from "../actions/charactersAction";
import { connect } from "react-redux"
import listenForOutsideClick from '../helper/listenerOutsideClick';

const CharacterSelect = (props) => {
    const { lineId, lines, characters } = props;
    const line = lines.byIds[lineId];

    const [showCharacterModal, setShowCharacterModal] = useState(false);

    const [isDropdownActive, setDropdownActive] = useState(false);
    // Hide Dropdown on Outside Click
    const menuRef = useRef(null)
    const [listening, setListening] = useState(false)
    useEffect(listenForOutsideClick(listening, setListening, menuRef, setDropdownActive))


    function toggleModal() {
        setShowCharacterModal(!showCharacterModal);
    }
    return (
        <div className="control" >
            <div ref={menuRef} className={(isDropdownActive ? "is-active" : "") + ' dropdown'} style={{ height: "38px" }}>
                <div onClick={() => setDropdownActive(!isDropdownActive)} className="dropdown-trigger button" aria-haspopup="true" aria-controls="dropdown-menu" style={{ width: '300px' }}>
                    <div className="level" style={{ width: '300px' }}>
                        <div className="level-left">
                            <div className="level-item">
                                {characters.byIds[line.character_id]?.image && <img className="image-character" src={characters.byIds[line.character_id]?.image?.public_path} />}
                            </div>
                        </div>
                        <div className="level-item">
                            {characters.byIds[line.character_id]?.name || 'Choisir un personnage'}
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
                            characters.ids.map((characterId, index) => {
                                return (
                                    <div key={index} className="dropdown-item" onClick={() => props.selectCharacter(characterId, lineId)}>
                                        {characters.byIds[characterId]?.image && <img className="image-character" src={characters.byIds[characterId]?.image?.public_path} />}
                                        {characters.byIds[characterId].name}
                                    </div>
                                );
                            })
                        }
                        <div className="dropdown-item" onClick={toggleModal}>Nouveau personnage</div>
                    </div>


                </div>
            </div>
            {
                showCharacterModal && <NewCharacterModal
                    toggleModal={toggleModal}
                    lineId={lineId}
                />
            }
        </div>
    )
}


const mapStateToProps = (state) => {
    return {
        sceneId: state.scenes.selectedId,
        lines: state.lines,
        characters: state.characters
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        selectCharacter: (characterId, lineId) => {
            dispatch(selectCharacter(characterId, lineId));
        }
    };
};



export default connect(mapStateToProps, mapDispatchToProps)(CharacterSelect);
