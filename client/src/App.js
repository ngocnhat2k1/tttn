import React, { Component } from 'react';
import logo from './logo.svg';
import './App.css';
import axios from 'axios';

class App extends Component {
  state = {
    message: ''
  };

  componentDidMount() {
    axios.get('/api/test')
         .then(result => this.setState({ message: result.data.message }))
  };

  render() {
    return(
      <div className="App">
        <header className="App-header">
          <h1>{ this.state.message }</h1>
          <img src={logo} className="App-logo" alt="logo" />
          <p>
            Edit <code>src/App.js</code> and save to reload.
          </p>
        </header>
      </div>
    )
  };
};

export default App;