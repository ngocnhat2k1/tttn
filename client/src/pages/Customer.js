import React, { Component } from 'react';
import '../App.css';
import Header from '../components/Header';
import Footer from '../components/Footer';
import Cookies from 'js-cookie';
import 'bootstrap/dist/css/bootstrap.min.css';
import CommonBanner from '../components/CommonBanner';
import MyAccountArea from '../components/MyAccountArea';

class Customer extends Component {
  render() {
    // if(Cookies.get('token') === undefined) {
    //   window.location.href = 'http://localhost:3000/login';
    // };

    return (
        <div className="App" style={{ padding: 0 }}>
          <Header />
          <CommonBanner namePage="Customer Dashboard"/>
          <MyAccountArea />
          <Footer />
        </div>
    )
  };
};

export default Customer;