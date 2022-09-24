import React, { Component } from 'react';
import '../App.css';
import axios from 'axios';
import Header from '../components/Header';
import Footer from '../components/Footer';
import GlobalStyles from '../components/GlobalStyles';
import 'bootstrap/dist/css/bootstrap.min.css';
import FacebookInfo from '../components/FacebookInfo';
import ProductIntroduction from '../components/ProductIntroduction';
import OfferCountdown from '../components/OfferCountdown'
import Banner from '../components/Banner'
import HotProduct from '../components/HotProduct';
class Home extends Component {
  state = {
    message: ''
  };

  componentDidMount() {
    axios.get('/api/test')
      .then(result => this.setState({ message: result.data.message }))
  };

  render() {
    return (
      <GlobalStyles>
        <div className="App" style={{padding: 0}}>
          <Header />
          <Banner />
          <ProductIntroduction/>
          <HotProduct />
          <OfferCountdown/>
          <FacebookInfo />
          <Footer />
        </div>
      </GlobalStyles>
    )
  };
};

export default Home;