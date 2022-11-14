import React from 'react'
import { FaTrash } from 'react-icons/fa'
import VoucherEditModal from "../VoucherEditModal/VoucherEditModal"

const ListVoucher = ({ currentVoucher }) => {
    return (
        <>
            {currentVoucher && currentVoucher.map((Voucher) => {
                return (
                    <tr key={Voucher.id}>
                        <td>
                            <a href="/invoice-one" className='text-primary'>{Voucher.id}</a>
                        </td>
                        <td>{Voucher.name}</td>
                        <td>{Voucher.usage}</td>
                        <td>{Voucher.percent}</td>
                        <td>{Voucher.expiredDate}</td>
                        <td>
                            <div className='edit_icon'>
                                <VoucherEditModal idDetail={Voucher.id} />
                            </div>
                            <div className='edit_icon'>
                                <FaTrash></FaTrash>
                            </div>
                        </td>

                    </tr>

                )
            })
            }
        </>
    )
}

export default ListVoucher