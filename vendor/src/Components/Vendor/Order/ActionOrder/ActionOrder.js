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
    const [idDelivery, setIdDelivery] = useState('')
    const [createdAt, setCreatedAt] = useState('')
    const [email, setEmail] = useState('')
    const [totalPrice, settotalPrice] = useState('')
    const [firstName, setFirstName] = useState('')
    const [lastName, setlastName] = useState('')

    const [avatar, setAvatar] = useState('')
    const [subscribed, setSubscribed] = useState('')
    const [address, setAddress] = useState('')
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
                setIdDelivery(response.data.data.idDelivery)
                setCreatedAt(response.data.data.createdAt)
                settotalPrice(response.data.data.totalPrice)
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
                    {/* <div className="modal-content">
                        <h2 className="title_modal">Detail Order {idOrder}</h2>
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

                    </div> */}
                    <div className="modal-content-order">
                        <Row>
                            <div className='detail-wrapper'>
                                <div className='detail-header'>
                                    <Row className='header-conten'>
                                        <h4>
                                            Detail Order {idDelivery}
                                        </h4>
                                    </Row>
                                    <div className='detail-bottom'>
                                        <ul>
                                            <li>
                                                <span>Issue Date: </span>
                                                <h6>{createdAt}</h6>
                                            </li>
                                        </ul>
                                        <ul>
                                            <li>
                                                <span>Email: </span>
                                                <h6>{email}</h6>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div className='detail-body'>
                                    <table className='table table-borderless mb-0'>
                                        <thead>
                                            <tr>
                                                <th scope='col'>#</th>
                                                <th scope='col'>Product</th>
                                                <th scope='col'>PRICE</th>
                                                <th scope='col'>Quantity</th>
                                                <th scope='col'>TOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope='col'>#</th>
                                                <th scope='col'>Productsssssssssssssssssssssssssssssss</th>
                                                <th scope='col'>PRICE</th>
                                                <th scope='col'>Quantity</th>
                                                <th scope='col'>TOTAL</th>
                                                {/* chạy vòng lặp ở đây  */}
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colSpan={2}></td>
                                                <td className='font-bold text-dark' colSpan={2}>Grand total</td>
                                                <td className='font-bold text-theme'>{totalPrice}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <div className='detail-footer text-right'>
                                        <div className='buttons'>
                                            <button className='theme-btn-one btn-black-overlay btn_sm'>Confirm</button>
                                            <button className='theme-btn-one btn-red-overlay btn_sm ml-2'>Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Row>
                    </div>
                </div >)}
        </div >
    )
}

export default ActionOrder