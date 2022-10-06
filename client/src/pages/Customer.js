import React, { Component } from 'react';
import '../App.css';
import Header from '../components/Header';
import Footer from '../components/Footer';
import GlobalStyles from '../components/GlobalStyles';
import 'bootstrap/dist/css/bootstrap.min.css';
import CommonBanner from '../components/CommonBanner';
import MyAccountArea from '../components/MyAccountArea';

class Customer extends Component {
  render() {
    return (
      <GlobalStyles>
        <div className="App" style={{ padding: 0 }}>
          <Header />
          <CommonBanner namePage="Customer Dashboard"/>
          <MyAccountArea />
          <Footer />
        </div>
      </GlobalStyles>
    )
  };
};

export default Customer;