import React from 'react'

const ListOrder = ({ currentOrder }) => {

    return (
        <>
            {currentOrder && currentOrder.map((Order, index) => {
                return (
                    <tr key={index}>
                        <td>
                            <a href="/invoice-one" className='text-primary'>{Order.id}</a>
                        </td>
                        <td>{Order.nameReceiver}</td>
                        <td>
                            {Order.deletedBy ? <span className='Cancelled'>Cancelled</span> : Order.status === 0 ? <span className='Pending'>Pending</span> : Order.status === 1 ? <span className='Confirmed'>Confirm</span> : <span className='Completed'>Completed</span>}
                        </td>
                        <td>{Order.totalPrice}₫</td>
                    </tr>
                )
            })
            }
        </>
    )
}

export default ListOrder