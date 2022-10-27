import '../App.css';
// import axios from 'axios';
import CommonBanner from '../components/CommonBanner';
import OrderCompleteArea from '../components/OrderCompleteArea';

function OrderComplete() {

  return (
    <>
      <CommonBanner namePage="Order Complete" />
      <OrderCompleteArea />
    </>
  )
};

export default OrderComplete;