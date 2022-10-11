import React, { Component } from 'react';
import '../App.css';
import Header from '../components/Header';
import Footer from '../components/Footer';

import 'bootstrap/dist/css/bootstrap.min.css';
import FacebookInfo from '../components/FacebookInfo';
import ProductIntroduction from '../components/ProductIntroduction';
import OfferCountdown from '../components/OfferCountdown'
import Banner from '../components/Banner'
import HotProduct from '../components/HotProduct';
import TrendingIntroduction from '../components/TrendingIntroduction';

class Home extends Component {
  render() {
    return (
        <div className="App" style={{ padding: 0 }}>
          <Header />
          <Banner />
          <ProductIntroduction />
          <HotProduct />
          <OfferCountdown />
          <TrendingIntroduction />
          <FacebookInfo />
          <Footer />
        </div>
    )
  };
};

export default Home;