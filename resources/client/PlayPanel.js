import React,{ useState, useEffect } from 'react'
import Play from './Play';
const PlayPanel = (props) => {
  const [plays, setPlays] = useState([]);

  useEffect(() => {
    const url = "http://localhost:3333/api/plays";

    const fetchData = async () => {
      try {
        const response = await fetch(url);
        const json = await response.json();
        console.log(json);
        setPlays(json);
      } catch (error) {
        console.log("error", error);
      }
    };

    fetchData();
  }, []);

  var PlaysElems = [];
  for (var i = 0; i < plays.length; i++) {
    PlaysElems.push(<Play key={i} play={plays[i]}/>);
  }  

  return (
    <div>
        <h1>Plays</h1>
        {PlaysElems}
    </div>
  )
}
export default PlayPanel;