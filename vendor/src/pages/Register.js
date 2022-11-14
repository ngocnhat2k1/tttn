import React, { Component } from 'react';
import '../App.css';
// import axios from 'axios';
import Header from '../Components/Header';
import Footer from '../Components/Footer';
import CommonBanner from '../Components/CommonBanner';
import RegisterArea from '../Components/RegisterArea';

class Register extends Component {

  render() {
    return (
      <div className="App" style={{ padding: 0 }}>
        <Header />
        <CommonBanner namePage="Register" />
        <RegisterArea />
        <Footer />
      </div>
    )
  };
};

export default Register;