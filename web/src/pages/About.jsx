import React from 'react'
import logo from '../assets/logo.svg'
import './About.css'

function App() {
  return (
    <div className="About">
      <div className="About-header">
        <img src={logo} className="About-logo" alt="logo" />
        <h2>Welcome to About</h2>
      </div>
      <p className="About-intro">
        To get started, edit <code>src/pages/Index.jsx</code> and save to reload.
      </p>
    </div>
  )
}

export default App
