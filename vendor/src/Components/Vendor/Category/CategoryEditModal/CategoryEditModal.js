import React, { useState } from "react";
import { FaEdit, FaTimes } from 'react-icons/fa'
import axios from 'axios';
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import Cookies from 'js-cookie';
import { useForm } from "react-hook-form";
import ModalConfirm from "../../ModalConfirm/ModalConfirm";
import "../../Modal.css";
import 'bootstrap/dist/css/bootstrap.min.css';

const CategoryEditModal = ({ idDetail }) => {
    const [modal, setModal] = useState(false);
    const [categoryName, setcategoryName] = useState('')
    const categoryInsessicon = sessionStorage.getItem("category");
    const [isChange, setIsChange] = useState(false)
    const [success, setSuccess] = useState("")
    const [message, setMessage] = useState('')
    const [notify, setNotify] = useState(false)
    const { register, handleSubmit, watch, formState: { errors }, reset } = useForm();
    const toggleModal = () => {
        setModal(!modal);
        axios
            .get(`http://127.0.0.1:8000/api/v1/categories/${idDetail}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`,
                },
            })
            .then((response) => {
                reset(response.data.data)
                sessionStorage.setItem("category", JSON.stringify(response.data.data))
            });
    };
    const closeModal = () => {
        setModal(!modal);
        setIsChange(false);
    }
    if (modal) {
        document.body.classList.add('active-modal')
    } else {
        document.body.classList.remove('active-modal')
    }

    const onSubmit = (data) => {
        const payload = data
        let { categoryId, createdAt, updatedAt, ...rest } = payload
        axios
            .put(`http://127.0.0.1:8000/api/v1/categories/${idDetail}/update`, rest, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`
                },
            })
            .then((response) => {
                setSuccess(response.data.success)
                if (success) {
                    setMessage(response.data.message)
                } else {
                    setMessage(response.data.errors)
                }
                setNotify(true)

            })
            .catch(function (error) {
                alert(error);
                console.log(error);
            });
    }
    return (
        <>
            <FaEdit onClick={toggleModal} className="btn-modal">
            </FaEdit>
            {modal && (
                <div className="modal">
                    <div onClick={closeModal} className="overlay"></div>
                    <div className="modal-content">
                        <h2 className="title_modal">Ch???nh s???a danh m???c {idDetail}</h2>
                        <form onSubmit={handleSubmit(onSubmit)}>
                            <Row>
                                <Col lg={12}>
                                    <div className="fotm-group">
                                        <label htmlFor="name">T??n danh m???c</label>
                                        <input type="text"
                                            className="form-control"
                                            id="name"
                                            {...register('name', {
                                                required: true,
                                                onChange: (e) => {
                                                    setcategoryName(e.target.value)
                                                    if (categoryName !== JSON.parse(categoryInsessicon).name) {
                                                        setIsChange(true)
                                                    }
                                                }
                                            })} />
                                        {errors.name?.type && <span className='error'>Kh??ng ???????c b??? tr???ng m???c n??y</span>}
                                    </div>
                                </Col>
                            </Row>
                            <div className="btn_right_table">
                                {isChange ? <button className="theme-btn-one bg-black btn_sm">L??u</button> : <button className="theme-btn-one bg-black btn_sm btn btn-secondary btn-lg" disabled>L??u</button>}
                            </div>
                        </form>

                        <button className="close close-modal" onClick={closeModal}><FaTimes /></button>

                    </div>
                </div>
            )
            }
            {notify && (
                <ModalConfirm message={message} success={success} />
            )}
        </>
    )
}

export default CategoryEditModal