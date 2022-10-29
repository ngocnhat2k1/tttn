import React from 'react'
import { FaEdit, FaTrash } from 'react-icons/fa'

const ListProducts = ({ listProducts }) => {
    return (
        <>
            {listProducts.map((Product) => {
                return (
                    <tr key={Product.id}>
                        <td>
                            <a>
                                <img width="70px" src={Product.img} alt="img" />
                            </a>
                        </td>
                        <td>
                            <a href="/product-details-one/1 ">{Product.name}</a>
                        </td>
                        <td>{Product.categories}</td>
                        <td>${Product.price}</td>
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