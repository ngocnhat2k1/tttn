import React, { useState } from 'react'
import { FaEdit, FaTrash } from 'react-icons/fa'
import CategoryEditModal from '../CategoryEditModal/CategoryEditModal'

const ListCategories = ({ currentCategory }) => {

    const handleDelte = () => {

    }
    return (
        <>
            {currentCategory.map((Category) => {
                return (

                    <tr key={Category.id}>
                        <td>
                            <a href="/invoice-one" className='text-primary'>{Category.id}</a>
                        </td>
                        <td>{Category.name}</td>

                        <td><CategoryEditModal idDetail={Category.id} />
                            <button >
                                <FaTrash onClick={handleDelte}></FaTrash>
                            </button>
                        </td>

                    </tr>

                )
            })
            }
        </>
    )
}

export default ListCategories