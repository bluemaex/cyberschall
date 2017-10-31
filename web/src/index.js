import React from 'react'
import { render } from 'react-dom'
import Cyberschall from './Cyberschall'
import registerServiceWorker from './registerServiceWorker'

const appEl = document.querySelector('[data-component="cyberschall"]')

render(React.createElement(Cyberschall, {}), appEl)
registerServiceWorker()
