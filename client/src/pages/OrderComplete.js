import '../App.css';
// import axios from 'axios';
import CommonBanner from '../components/CommonBanner';
import OrderCompleteArea from '../components/OrderCompleteArea';

function OrderComplete() {

  return (
    <>
      <CommonBanner namePage="Đặt hàng thành công" />
      <OrderCompleteArea />
    </>
  )
};

export default OrderComplete;