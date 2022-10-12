import React, { Component } from 'react';
import '../App.css';
// import axios from 'axios';
import Header from '../components/Header';
import Footer from '../components/Footer';
import 'bootstrap/dist/css/bootstrap.min.css';
import CommonBanner from '../components/CommonBanner';
import LoginArea from '../components/LoginArea';
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
  if(Cookies.get('token') !== undefined) {
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