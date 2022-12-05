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
                        {Voucher.deleted === 1 ? <td>Đã xoá</td> : <td>Khả dụng</td>}
                        <td>
                            <div className='edit_icon'>
                                <VoucherEditModal idDetail={Voucher.voucherId} />
                            </div>
                            <div className='edit_icon'>
                                <DeleteVoucher idDetail={Voucher.voucherId} nameDetail={Voucher.name} />
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