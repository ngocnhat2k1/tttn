import React from 'react'
import ActionOrder from '../ActionOrder/ActionOrder'

const ListOrder = ({ currentOrder }) => {
    return (
        <>
            {currentOrder && currentOrder.map((Order, index) => {
                return (
                    <tr key={index}>
                        <td>
                            {Order.id_delivery}
                        </td>
                        <td>{Order.firstName} {Order.lastName}</td>
                        <td>{Order.nameReceiver}</td>
                        <td>{Order.phoneReceiver}</td>
                        <td>{Order.address}</td>
                        <td>
                            {Order.deletedBy ? <span className='Cancelled'>Cancelled</span> : Order.status === 0 ? <span className='Pending'>Pending</span> : Order.status === 1 ? <span className='Confirmed'>Confirm</span> : <span className='Completed'>Completed</span>}
                        </td>
                        <td>{Order.price}₫</td>
                        {console.log(currentOrder)}
                        <td><ActionOrder idOrder={Order.orderId} idCustomer={Order.lastName + Order.fistName} /> </td>
                    </tr>
                )
            })
            }
        </>
    )
}

export default ListOrder