import React from 'react'
import UserDetail from '../UserDetail/UserDetail'


const ListUsers = ({ listUsers }) => {
    return (
        <>
            {listUsers.map((User) => {

                return (
                    <tr key={User.id}>
                        <td>{User.id}</td>
                        <td>
                            <a>
                                {User.avatar ? <img width="70px" src={User.avatar} alt="img" /> : <img width="70px" src={User.defaultAvatar} alt="img" />}
                            </a>
                        </td>
                        <td>
                            <a href="/product-details-one/1 ">{User.firstName} {User.lastName}</a>
                        </td>
                        <td>{User.email}</td>
                        <td><UserDetail idDetail={User.id} firstNameDetail={User.firstName} lastNameDetail={User.lastName} /></td>


                    </tr>
                )
            })
            }
        </>
    )
}

export default ListUsers