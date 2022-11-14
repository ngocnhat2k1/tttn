import React from 'react'
import { FaEdit, FaTrash } from 'react-icons/fa'
import DeleteProduct from '../DeleteProduct/DeleteProduct'
import ProductEditModal from '../ProductEditModal/ProductEditModal'

const ListProducts = ({ listProducts }) => {
    return (
        <>
            {listProducts && listProducts.map((Product) => {
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
                        <td>
                            {Product.categories.map((Categories, i) => {
                                if (i + 1 === Product.categories.length) {
                                    return (Categories.name)
                                }
                                else {
                                    return `${Categories.name}, `
                                }
                            })}
                        </td>
                        <td>${Product.price}</td>
                        <td>{Product.precentSale}</td>
                        {Product.status === 1 ? <td>Còn hàng</td> : <td>hết hàng</td>}
                        {/* {console.log('cc', Product.deletedAt)} */}
                        {Product.deletedAt === 1 ? <td>đã xoá</td> : <td>chưa xoá</td>}
                        <td>
                            <div className='edit_icon'><ProductEditModal idDetail={Product.id} /></div>
                            <div className='edit_icon'><DeleteProduct idDetail={Product.id} nameDetail={Product.name} /></div>
                        </td>
                    </tr>
                )
            })
            }
        </>

    )
}

export default ListProducts