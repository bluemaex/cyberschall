import React from 'react'
import { Link } from 'react-router-dom'
import logo from '../assets/logo.svg'
import './Index.css'

function App() {
  return (
    <div className="Index">
      <div className="Index-header">
        <img src={logo} className="Index-logo" alt="logo" />
        <h2>Welcome to React</h2>
      </div>
      <p className="Index-intro">
        To know more go to <Link to="/about-us">About</Link>
      </p>
    </div>
  )
}

export default App
