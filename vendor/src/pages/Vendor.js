import React, { Component, useEffect } from 'react';
import '../App.css';
import Header from '../Components/Header';
import Footer from '../Components/Footer';
import CommonBanner from '../Components/CommonBanner';
import GlobalStyles from '../Components/GlobalStyles';
import 'bootstrap/dist/css/bootstrap.min.css';
import VendorArea from '../Components/Vendor/VendorArea';
import axios from 'axios';
import Cookies from 'js-cookie';
function Vendor() {
  // useEffect(() => {
  //   axios
  //     .get(`http://localhost:8000/api/retrieveToken`, {
  //       headers: {
  //         Authorization: `Bearer ${Cookies.get('token')}`,
  //       },
  //     })
  //     .then((response) => {
  //       if (!response.data.success) {
  //         window.location.href = 'http://localhost:3000/login';
  //       }
  //     })
  //     .catch(function (error) {
  //       console.log(error);
  //     });
  // }, []);

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

export default Vendor;