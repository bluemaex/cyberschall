import React from 'react'
import { Provider } from 'react-redux'
import { Route, Switch } from 'react-router-dom'
import { ConnectedRouter } from 'react-router-redux'
import store, { history } from './store'

import Index from './pages/Index'
import About from './pages/About'
import './styles/index.css'

if (window && window.addEventListener) {
  window.addEventListener('online', () => store.dispatch({ type: 'NETWORK_ONLINE' }))
  window.addEventListener('offline', () => store.dispatch({ type: 'NETWORK_OFFLINE' }))
}

const Cyberschall = () => (
  <Provider store={store}>
    <div className="cyberschall full-height">
      <ConnectedRouter history={history}>
        <Switch>
          <Route exact path="/" component={Index} />
          <Route exact path="/about-us" component={About} />
        </Switch>
      </ConnectedRouter>
    </div>
  </Provider>
)

export default Cyberschall
