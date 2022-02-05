import React from 'react'
import { isPropertySignature } from 'typescript'
export default function Scene(props) {
    
  return (
    <div>
      Hello Scene {props.scene.name}
    </div>
  )
}