import React, { Component } from 'react';
import '../App.css';
// import axios from 'axios';
import Header from '../components/Header';
import Footer from '../components/Footer';

import 'bootstrap/dist/css/bootstrap.min.css';
import CommonBanner from '../components/CommonBanner';
import RegisterArea from '../components/RegisterArea';

class Register extends Component {
  // state = {
  //   message: ''
  // };

  // componentDidMount() {
  //   axios.get('/api/test')
  //     .then(result => this.setState({ message: result.data.message }))
  // };

  render() {
    return (
        <div className="App" style={{padding: 0}}>
          <Header />
          <CommonBanner namePage="Register"/>
          <RegisterArea />
          <Footer />
        </div>
    )
  };
};

export default Register;