import React from 'react'
import { FaEdit, FaTrash } from 'react-icons/fa'

const ListVoucher = ({ currentVoucher }) => {
    return (
        <>
            {currentVoucher.map((Voucher) => {
                return (
                    <tr key={Voucher.id}>
                        <td>
                            <a href="/invoice-one" className='text-primary'>{Voucher.id}</a>
                        </td>
                        <td>{Voucher.name}</td>
                        {Voucher.usage ? <td>{Voucher.usage}</td> : <td>hehe</td>}
                        <td>{Voucher.percent}</td>
                        <td>{Voucher.expiredDate}</td>
                        <td><a href="/vendor/add-products">
                            <FaEdit ></FaEdit>
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

export default ListVoucher