import React from 'react'
import { FaEdit, FaTrash } from 'react-icons/fa'

const ListProducts = ({ currentProduct }) => {
    return (

        <>
            {
                currentProduct.map((Product, index) => {
                    return (
                        <tr key={index}>
                            <td><a><img width="70px" src={Product.img} alt="img" /></a></td>
                            <td><a href="/product-details-one/1">{Product.name}</a></td>
                            <td>{Product.categories}</td>
                            <td>${Product.price}</td>
                            <td>{Product.Stock}</td>
                            <td>{Product.precentSale}</td>
                            <td><a href="/vendor/add-products">
                                <FaEdit></FaEdit>
                            </a>
                                <button type="">
                                    <FaTrash></FaTrash>
                                </button>
                            </td>
                        </tr>
                    )
                })
            }
        </>

    )
}

export default ListProducts