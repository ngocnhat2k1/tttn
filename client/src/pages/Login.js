import React, { Component } from 'react';
import '../App.css';
// import axios from 'axios';
import Header from '../components/Header';
import Footer from '../components/Footer';
import GlobalStyles from '../components/GlobalStyles';
import 'bootstrap/dist/css/bootstrap.min.css';
import CommonBanner from '../components/CommonBanner';
import LoginArea from '../components/LoginArea';

class Login extends Component {
  // state = {
  //   message: ''
  // };

  // componentDidMount() {
  //   axios.get('/api/test')
  //     .then(result => this.setState({ message: result.data.message }))
  // };

  render() {
    return (
      <GlobalStyles>
        <div className="App" style={{padding: 0}}>
          <Header />
          <CommonBanner namePage="Login"/>
          <LoginArea />
          <Footer />
        </div>
      </GlobalStyles>
    )
  };
};

export default Login;