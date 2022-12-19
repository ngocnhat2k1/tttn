import React from 'react'
import CheckoutOrder from '../components/CheckoutOrder/CheckoutOrder';
import CommonBanner from '../components/CommonBanner';

const CheckoutOrderPage = () => {
    return (
        <>
            <CommonBanner namePage="Đặt hàng" />
            <CheckoutOrder />
        </>
    )
}

export default CheckoutOrderPage