import React, { Component } from 'react';
import '../App.css';
// import axios from 'axios';
import Header from '../Components/Header';
import Footer from '../Components/Footer';
import CommonBanner from '../Components/CommonBanner';
import LoginArea from '../Components/LoginArea';
import Cookies from 'js-cookie';

class Login extends Component {

  // state = {
  //   message: ''
  // };

  // componentDidMount() {
  //   axios.get('/api/test')
  //     .then(result => this.setState({ message: result.data.message }))
  // };

  render() {
    if (Cookies.get('token') !== undefined) {
      window.location.href = 'http://localhost:3000/my-account';
    };

    return (
      <div className="App" style={{ padding: 0 }}>
        <Header />
        <CommonBanner namePage="Login" />
        <LoginArea />
        <Footer />
      </div>
    )
  };
};

export default Login;