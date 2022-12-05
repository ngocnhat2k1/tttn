import '../App.css';
// import axios from 'axios';
import FacebookInfo from '../components/FacebookInfo';
import CommonBanner from '../components/CommonBanner';
import ShopMainArea from '../components/ShopMainArea';

function Shop() {
    return (
        <>
            <CommonBanner namePage="Cửa Hàng" />
            <ShopMainArea />
        </>
    )
};

export default Shop;