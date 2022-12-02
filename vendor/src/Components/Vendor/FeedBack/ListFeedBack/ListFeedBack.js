import React from 'react'

const ListFeedBack = ({ currentFeedBack }) => {
    return (
        <>
            {currentFeedBack && currentFeedBack.map((FeedBack) => {
                return (
                    <tr key={FeedBack.id}>
                        <td>
                            {FeedBack.firstName} {FeedBack.lastName}
                        </td>
                        <td>{FeedBack.productName}</td>
                        <td>{FeedBack.comment}</td>
                        <td>{FeedBack.createdAt}</td>
                    </tr>

                )
            })
            }
        </>
    )
}

export default ListFeedBack