import React, { useState } from 'react'
import { FaListAlt, FaTimes, FaImage } from 'react-icons/fa'
import axios from 'axios';
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import Cookies from 'js-cookie';
import { useForm } from "react-hook-form";
import 'bootstrap/dist/css/bootstrap.min.css';

const UserDetail = ({ idDetail, firstNameDetail, lastNameDetail }) => {
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
    const closeModal = () => {
        setModal(!modal);
    }
    const handleImage = (e) => {
        const file = e.target.files[0];

        const Reader = new FileReader();

        Reader.readAsDataURL(file);

        Reader.onload = () => {
            if (Reader.readyState === 2) {
                setAvatar(Reader.result);

            }
        };
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
                                <Col lg={12}>
                                    <div className='image-input'>
                                        <img src={avatar} alt="img" className='image-preview' />

                                    </div>
                                </Col>
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
                                <Col lg={6}>
                                    <div className="fotm-group">
                                        <label htmlFor="lastName">Last Name</label>
                                        <input type="text"
                                            className="form-control"
                                            id="lastName"
                                            value={lastName}
                                            {...register('lastName', { required: true, disabled: true })} />
                                    </div>
                                </Col>
                                <Col lg={6}>
                                    <div className="fotm-group">
                                        <label htmlFor="email">Email</label>
                                        <input type="text"
                                            className="form-control"
                                            id="email"
                                            value={email}
                                            {...register('email', { required: true, disabled: true })} />
                                    </div>
                                </Col>
                                <Col lg={6}>
                                    <div className="fotm-group">
                                        <label htmlFor="Subscribed">Subscribed</label>
                                        <input type="text"
                                            className="form-control"
                                            id="Subscribed"
                                            value={subscribed}
                                            {...register('subscribed', { required: true, disabled: true })} />
                                    </div>
                                </Col>
                            </Row>
                            {/* {<div className="btn_left_table">
                                <button onClick={closeModal} className="theme-btn-one bg-black btn_sm">Close</button>
                            </div>} */}
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

export default UserDetail