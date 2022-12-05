import OrderDetailArea from "../components/OrderDetailArea"
import CommonBanner from "../components/CommonBanner"

function OrderDetail() {
    return (
        <>
            <CommonBanner namePage='Chi tiết đơn hàng' />
            <OrderDetailArea />
        </>
    )
}

export default OrderDetail