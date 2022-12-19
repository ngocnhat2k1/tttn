import axios from 'axios'
import React, { useState, useEffect, useReducer } from 'react'
import { FaEdit, FaTrash } from 'react-icons/fa'
import CategoryEditModal from '../CategoryEditModal/CategoryEditModal'
import Cookies from 'js-cookie';
import DeleteCategory from '../DeleteCategory/DeleteCategory';

const ListCategories = ({ currentCategory }) => {


    return (
        <>
            {currentCategory && currentCategory.map((Category) => {
                return (

                    <tr key={Category.id}>
                        <td>
                            <a href="/invoice-one" className='text-primary'>{Category.id}</a>
                        </td>
                        <td>{Category.name}</td>

                        <td>
                            <div className='edit_icon'> <CategoryEditModal idDetail={Category.id} /></div>
                            <div className='edit_icon'><DeleteCategory idDetail={Category.id} nameDetail={Category.name} /></div>
                        </td>

                    </tr>

                )
            })
            }
        </>
    )
}

export default ListCategories