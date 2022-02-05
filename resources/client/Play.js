import React,{ useState } from 'react'
import Scene from './Scene';
const Play = (props) => {
  const [playName, setPlayName] = useState(props.play.name);

  var scenesElems = [];
  for (var i = 0; i < props.play.scenes.length; i++) {
    scenesElems.push(<Scene key={i} scene={props.play.scenes[i]}/>);
  }
  return (
    <div>
        <h2>Play: {props.play.name}</h2>
        {scenesElems}
    </div>
  )
}
export default Play;