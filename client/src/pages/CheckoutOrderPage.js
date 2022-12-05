import React from 'react'
import CheckoutOrder from '../components/CheckoutOrder/CheckoutOrder';
import CommonBanner from '../components/CommonBanner';

const CheckoutOrderPage = () => {
    return (
        <>
            <CommonBanner namePage="Checkout Order" />
            <CheckoutOrder />
        </>
    )
}

export default CheckoutOrderPage