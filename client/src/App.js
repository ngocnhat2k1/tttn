import React, { Component } from 'react';
import './App.css';
import axios from 'axios';
import Header from './components/Header';
import GlobalStyles from './components/GlobalStyles';
// import { Button } from 'react-bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import NavBar from './components/Header/NavBar/NavBar';

class App extends Component {
  state = {
    message: ''
  };

  componentDidMount() {
    axios.get('/api/test')
      .then(result => this.setState({ message: result.data.message }))
  };

  render() {
    return (
      
      <GlobalStyles>
        <div className="App">
          <Header />
          <NavBar/>
        </div>
      </GlobalStyles>
    )
  };
};

export default App;