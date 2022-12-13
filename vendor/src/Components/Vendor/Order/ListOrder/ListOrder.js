import React from 'react'
import { formatter } from '../../../../until/FormatterVND'
import ActionOrder from '../ActionOrder/ActionOrder'

const ListOrder = ({ currentOrder }) => {
    return (
        <>
            {currentOrder && Object.values(currentOrder).map((Order, index) => {
                return (
                    <tr key={index}>
                        <td>
                            {Order.orderId}
                        </td>
                        <td>{Order.firstName} {Order.lastName}</td>
                        <td>{Order.nameReceiver}</td>
                        <td>{Order.phoneReceiver}</td>
                        <td>{Order.address}</td>
                        <td>
                            {Order.status}
                        </td>
                        <td>{formatter.format(Order.price)}</td>
                        <td><ActionOrder idOrder={Order.orderId} idCustomer={Order.customerId} /> </td>
                    </tr>
                )
            })
            }
        </>
    )
}

export default ListOrder