import React from 'react'
import { FaTrash } from 'react-icons/fa'
import DeleteVoucher from '../DeleteVoucher/DeleteVoucher'
import VoucherEditModal from "../VoucherEditModal/VoucherEditModal"

const ListVoucher = ({ currentVoucher }) => {
    return (
        <>
            {currentVoucher && currentVoucher.map((Voucher) => {
                return (
                    <tr key={Voucher.voucherId}>
                        <td>
                            <a href="/invoice-one" className='text-primary'>{Voucher.voucherId}</a>
                        </td>
                        <td>{Voucher.name}</td>
                        <td>{Voucher.usage}</td>
                        <td>{Voucher.percent}</td>
                        <td>{Voucher.expiredDate}</td>
                        {Voucher.deleted === 1 ? <td>Deleted</td> : <td>Availability</td>}
                        <td>
                            <div className='edit_icon'>
                                <VoucherEditModal idDetail={Voucher.id} />
                            </div>
                            <div className='edit_icon'>
                                <DeleteVoucher idDetail={Voucher.id} nameDetail={Voucher.name} />
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