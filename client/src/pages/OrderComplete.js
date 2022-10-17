import React, { Component } from 'react';
import '../App.css';
// import axios from 'axios';
import Header from '../components/Header';
import Footer from '../components/Footer';
import CommonBanner from '../components/CommonBanner';
import OrderCompleteArea from '../components/OrderCompleteArea'; 

class OrderComplete extends Component {
//   state = {
//     message: ''
//   };

//   componentDidMount() {
//     axios.get('/api/test')
//       .then(result => this.setState({ message: result.data.message }))
//   };

  render() {
    return (
        <div className="App" style={{padding: 0}}>
          <Header />
          <CommonBanner namePage="Order Complete"/>
          <OrderCompleteArea />
          <Footer />
        </div>
    )
  };
};

export default OrderComplete;