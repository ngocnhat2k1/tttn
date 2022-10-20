import React from 'react'

const ListOrder = ({ currentOrder }) => {
    return (
        <>
            {currentOrder.map((Order, index) => {
                return (
                    <tr key={index}>
                        <td>
                            <a href="/invoice-one" className='text-primary'>{Order.OrderId}</a>
                        </td>
                        <td>{Order.ProductDetails}</td>
                        <td>
                            <span className={Order.Status}>{Order.Status}</span>
                        </td>
                        <td>{Order.Price}</td>
                    </tr>
                )
            })
            }
        </>
    )
}

export default ListOrder