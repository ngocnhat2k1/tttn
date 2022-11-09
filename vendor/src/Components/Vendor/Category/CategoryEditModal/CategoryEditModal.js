import React, { useState, useEffect } from "react";
import { FaEdit, FaTimes } from 'react-icons/fa'
import axios from 'axios';
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import Cookies from 'js-cookie';
import { useForm } from "react-hook-form";
import "./Modal.css";
import 'bootstrap/dist/css/bootstrap.min.css';

const CategoryEditModal = ({ idDetail }) => {
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
    // lấy tên mặc định của category
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
    }, []);

    const onSubmit = (data) => {
        axios
            .post(`http://127.0.0.1:8000/api/v1/categories/${idDetail}/update`, data, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`
                },
            })
    }
    const onChangeName = (e) => {
        setCategoryName(e.target.value)
    }
    return (
        <>
            <FaEdit onClick={toggleModal} className="btn-modal">
            </FaEdit>

            {modal && (
                <div className="modal">
                    <div onClick={toggleModal} className="overlay"></div>
                    <div className="modal-content">
                        <h2 className="title_modal">Edit Category {idDetail}</h2>
                        <form onSubmit={handleSubmit(onSubmit)}>
                            <Row>
                                <Col lg={12}>
                                    <div className="fotm-group">
                                        <label for="name">Category Name</label>
                                        <input type="text"
                                            className="form-control"
                                            id="name"
                                            value={categoryName}
                                            {...register('name', { required: true, onChange: onChangeName })} />
                                    </div>
                                </Col>
                            </Row>
                            <div class="btn_right_table">
                                <button class="theme-btn-one bg-black btn_sm">Save</button>
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

export default CategoryEditModal