import React, { Component } from 'react';
import '../App.css';
import Header from '../Components/Header';
import Footer from '../Components/Footer';
import CommonBanner from '../Components/CommonBanner';
import GlobalStyles from '../Components/GlobalStyles';
import 'bootstrap/dist/css/bootstrap.min.css';
import VendorArea from '../Components/Vendor/VendorArea';
class Home extends Component {


  render() {
    return (
      <GlobalStyles>
        <div className="App" style={{ padding: 0 }}>
          <Header />
          <CommonBanner namePage={`Vendor`} />
          <VendorArea />
          <Footer />
        </div>
      </GlobalStyles>
    )
  };
};

export default Home;