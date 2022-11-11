import React, { useState, useEffect } from "react";
import { FaEdit, FaTrash, FaTimes } from 'react-icons/fa'

import axios from 'axios';
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import Cookies from 'js-cookie';
import { useForm } from "react-hook-form";
import "./Modal.css";
import 'bootstrap/dist/css/bootstrap.min.css';
import { Link } from "react-router-dom";
import ListCategories from "../ListCategories/ListCategories";

const DeleteCategory = ({ idDetail, nameDetail }) => {
    const [modal, setModal] = useState(false);
    const [categoryName, setCategoryName] = useState('')
    const { register, handleSubmit, watch, formState: { errors } } = useForm();
    const toggleModal = () => {
        setModal(!modal);
    };

    if (modal) {
        document.body.classList.add('active-modal')
    } else {
        document.body.classList.remove('active-modal')
    }
    useEffect(() => {
        axios
            .get(`http://127.0.0.1:8000/api/v1/categories/${idDetail}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`,
                },
            })

            .then((response) => {
                setCategoryName(response.data.name);
            });
    }, [idDetail]);

    const onSubmit = (data) => {
        setModal(!modal);
        axios
            .delete(`http://127.0.0.1:8000/api/v1/categories/${idDetail}/destroy`,
                {
                    headers: {
                        Authorization: `Bearer ${Cookies.get('adminToken')}`,
                    },
                },)
            .then((response) => {

                // alert(response.data.success);
                window.location.reload(false)
                console.log('kkhi da bam')

            })
            .catch(function (error) {
                alert(error);
                console.log(error);
            });
    }
    return (
        <>
            <FaTrash onClick={toggleModal} className="btn-modal"></FaTrash>
            {
                modal && (
                    <div className="modal">
                        <div onClick={toggleModal} className="overlay"></div>
                        <div className="modal-content">
                            <h2 className="title_modal">Confirm delete Category <p>{nameDetail}</p></h2>
                            <form onSubmit={handleSubmit(onSubmit)}>
                                <div className="btn_right_table">

                                    <button className="theme-btn-one bg-black btn_sm">Delete </button>

                                </div>
                            </form>

                            <button className="close close-modal" onClick={toggleModal}><FaTimes /></button>

                        </div>
                    </div>
                )
            }
        </>

    )
}

export default DeleteCategory