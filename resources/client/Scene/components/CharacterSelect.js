
import React, { useState } from 'react'

export default function CharacterSelect(props) {
    const [isDropdownActive, setDropdownActive] = useState(false);

    return (
        <div className="control">
            <div className={(isDropdownActive ? "is-active" : "") + ' dropdown'} style={{ height: "38px" }}>
                <div onClick={() => setDropdownActive(!isDropdownActive)} className="dropdown-trigger button" aria-haspopup="true" aria-controls="dropdown-menu" style={{ width: '300px' }}>
                    <div className="level" style={{ width: '300px' }}>
                        <div className="level-left">
                            <div className="level-item">
                                {props.characterSelected?.image ?? <img className="image-character" src={props.characterSelected?.image?.publicPath} />}
                            </div>
                        </div>
                        <div className="level-item">
                            {props.characterSelected?.name || 'Choisir un personnage'}
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
                        <div className="dropdown-item">Nouveau personnage</div>
                    </div>


                </div>
            </div>
        </div>
    )
}
