import React from 'react'

const ListFeedBack = ({ currentFeedBack }) => {
    return (
        <>
            {currentFeedBack && currentFeedBack.map((FeedBack) => {
                console.log(FeedBack)
                return (
                    <tr key={FeedBack.id}>
                        <td>
                            {FeedBack.firstName} {FeedBack.lastName}
                        </td>
                        <td>{FeedBack.productName}</td>
                        <td>{FeedBack.comment}</td>
                        <td>{FeedBack.createdAt}</td>
                        <td>
                            {/* <div className='edit_icon'>
                                <VoucherEditModal idDetail={Voucher.id} />
                            </div>
                            <div className='edit_icon'>
                                <DeleteVoucher idDetail={Voucher.id} nameDetail={Voucher.name} />
                            </div> */}
                        </td>

                    </tr>

                )
            })
            }
        </>
    )
}

export default ListFeedBack