import React, { useState } from 'react'
import { FaEdit, FaTrash } from 'react-icons/fa'
import CategoryEditModal from '../CategoryEditModal/CategoryEditModal'

const ListCategories = ({ currentCategory }) => {


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

export default ListCategories