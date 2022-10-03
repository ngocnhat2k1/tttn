import React, { Component } from 'react';
import '../App.css';
// import axios from 'axios';
import Header from '../components/Header';
import Footer from '../components/Footer';
import GlobalStyles from '../components/GlobalStyles';
import 'bootstrap/dist/css/bootstrap.min.css';
import FacebookInfo from '../components/FacebookInfo';
import CommonBanner from '../components/CommonBanner';
import ShopMainArea from '../components/ShopMainArea';

class Shop extends Component {
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
                <div className="App" style={{ padding: 0 }}>
                    <Header />
                    <CommonBanner namePage="Shop"/>
                    <ShopMainArea />
                    <FacebookInfo />
                    <Footer />
                </div>
            </GlobalStyles>
        )
    };
};

export default Shop;