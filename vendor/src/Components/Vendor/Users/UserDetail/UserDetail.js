import React, { useState } from 'react'
import { FaListAlt, FaTimes } from 'react-icons/fa'
import axios from 'axios';
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import Cookies from 'js-cookie';
import { useForm } from "react-hook-form";
import "./Modal.css";
import 'bootstrap/dist/css/bootstrap.min.css';

const UserDetail = ({ idDetail }) => {
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
            .get(`http://127.0.0.1:8000/api/v1/users/${idDetail}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`,
                },
            })

            .then((response) => {
                console.log(response.data.data.firstName)
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
        console.log(firstName)
        console.log(lastName)
        console.log(email)
        console.log(subscribed)
    };


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
                        <h2 className="title_modal">User Profile {idDetail}</h2>
                        <form >
                            <Row>
                                <Col lg={6}>
                                    <div className="fotm-group">
                                        <label htmlFor="name">First Name</label>
                                        <input type="text"
                                            className="form-control"
                                            id="name"
                                            value={firstName}
                                            {...register('name', { required: true })} />
                                    </div>
                                </Col>
                                <Col lg={6}>
                                    <div className="fotm-group">
                                        <label htmlFor="percent">Last Name</label>
                                        <input type="number"
                                            className="form-control"
                                            id="percent"
                                            value={lastName}
                                            {...register('percent', { required: true })} />
                                    </div>
                                </Col>
                                <Col lg={6}>
                                    <div className="fotm-group">
                                        <label htmlFor="usage">Email</label>
                                        <input type="number"
                                            className="form-control"
                                            id="usage"
                                            value={email}
                                            {...register('usage', { required: true })} />
                                    </div>
                                </Col>
                                <Col lg={6}>
                                    <div className="fotm-group">
                                        <label htmlFor="name">Voucher Name</label>
                                        <input type="text"
                                            className="form-control"
                                            id="name"
                                            value={subscribed}
                                            {...register('name', { required: true })} />
                                    </div>
                                </Col>

                            </Row>
                            <div className="btn_right_table">
                                <button className="theme-btn-one bg-black btn_sm">Close</button>
                            </div>
                        </form>

                        <button className="close close-modal" onClick={toggleModal}><FaTimes /></button>

                    </div>
                </div>)}
        </div>
    )
}

export default UserDetail