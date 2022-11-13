import React, { useState } from 'react'
import { FaListAlt, FaTimes, FaImage } from 'react-icons/fa'
import axios from 'axios';
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import Cookies from 'js-cookie';
import { useForm } from "react-hook-form";
import 'bootstrap/dist/css/bootstrap.min.css';

const ActionOrder = ({ idOrder, idCustomer }) => {
    const [modal, setModal] = useState(false);
    const [firstName, setFirstName] = useState('')
    const [lastName, setlastName] = useState('')
    const [email, setEmail] = useState('')
    const [avatar, setAvatar] = useState('')
    const [subscribed, setSubscribed] = useState('')
    const { register, handleSubmit, watch, formState: { errors } } = useForm();
    const toggleModal = () => {
        setModal(!modal);
        axios
            .get(`http://127.0.0.1:8000/api/v1/users/${idCustomer}/orders/${idOrder}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`,
                },
            })

            .then((response) => {
                setFirstName(response.data.data.firstName);
                setlastName(response.data.data.lastName)
                setEmail(response.data.data.email)
                if (response.data.data.avatar) {
                    setAvatar(response.data.data.avatar)
                } else {
                    setAvatar(response.data.data.defaultAvatar)
                }
                if (response.data.data.subscribed == 1) {
                    setSubscribed('Yes')
                } else {
                    setSubscribed('No')
                }
            });
    };

    const onSubmit = (data) => {
        console.log(data)
        axios
            .put(`http://127.0.0.1:8000/api/v1/voucher/${idOrder}/update`, data, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`
                },
            })
            .then((response) => {
                alert(response.data.success);
                console.log(response.data.error);
                if (response.data.success === true) {
                    window.location.reload = (false);
                }
            })
            .catch(function (error) {
                alert(error);
                console.log(error);
            });
    }

    const closeModal = () => {
        setModal(!modal);
    }

    if (modal) {
        document.body.classList.add('active-modal')
    } else {
        document.body.classList.remove('active-modal')
    }
    return (
        <div><FaListAlt onClick={toggleModal} />
            {modal && (
                <div className="modal">
                    <div onClick={toggleModal} className="overlay"></div>
                    <div className="modal-content">
                        <h2 className="title_modal">User Profile {idOrder}</h2>
                        <form onSubmit={handleSubmit(onSubmit)}>
                            <Row>
                                <Col lg={6}>
                                    <div className="fotm-group">
                                        <label htmlFor="firstName">First Name</label>
                                        <input type="text"
                                            className="form-control"
                                            id="firstName"
                                            value={firstName}
                                            {...register('firstName', { required: true, disabled: true })} />
                                    </div>
                                </Col>

                            </Row>
                            {<div className="btn_left_table">
                                <button onClick={closeModal} className="theme-btn-one bg-black btn_sm">Close</button>
                            </div>}
                            <div className="btn_right_table">
                                <button onClick={closeModal} className="theme-btn-one bg-black btn_sm">Close</button>
                            </div>
                        </form>

                        <button className="close close-modal" onClick={closeModal}><FaTimes /></button>

                    </div>
                </div>)}
        </div>
    )
}

export default ActionOrder