import React, { useState, useEffect } from 'react'
import { FaListAlt, FaTimes, FaImage } from 'react-icons/fa'
import axios from 'axios';
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import Cookies from 'js-cookie';
import { useForm } from "react-hook-form";
import 'bootstrap/dist/css/bootstrap.min.css';
import formatDate from '../../../../until/formatDateTime';

const ActionOrder = ({ idOrder, idCustomer }) => {
    const [modal, setModal] = useState(false);
    const [idDelivery, setIdDelivery] = useState('')
    const [createdAt, setCreatedAt] = useState('')
    const [email, setEmail] = useState('')
    const [totalPrice, settotalPrice] = useState('')
    const [listProducts, setListProducts] = useState([])
    const [address, setAddress] = useState('')
    const [deletedBy, setDeletedBy] = useState()
    const [totalPriceCart, settotalPriceCart] = useState(0)
    const [state, setState] = useState('')
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
                setIdDelivery(response.data.data.order.idDelivery)
                setCreatedAt(response.data.data.order.createdAt)
                settotalPrice(response.data.data.order.totalPrice)
                setListProducts(response.data.data.products)
                setEmail(response.data.data.customer.email)
                setState(response.data.data.order.status)
                setAddress(response.data.data.order.address)
                setDeletedBy(response.data.data.order.deleted_by)
            });
    };

    const closeModal = () => {
        setModal(!modal);
    }
    const handleState = () => {
        console.log(Cookies.get('adminToken'))
        axios
            .put(`http://127.0.0.1:8000/api/v1/orders/${idOrder}/update/status=${state + 1}`, 1, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`,
                },
            })
            .then((response) => {
                alert(response.data.message)
            })
    }
    const handleCancel = () => {
        axios
            .delete(`http://127.0.0.1:8000/api/v1/users/${idCustomer}/orders/${idOrder}/destroy=1`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`,
                },
            })
            .then((response) => {
                alert(response.data.success)
            })
            .catch((err) => { console.log(err) })

    }
    if (modal) {
        document.body.classList.add('active-modal')
    } else {
        document.body.classList.remove('active-modal')
    }
    const [couter, setcouter] = useState(0)
    useEffect(() => {
        listProducts.map((product) => {
            if (couter < 1) {
                settotalPriceCart(totalPriceCart => totalPriceCart + (product.price * ((100 - product.percentSale) / 100)) * product.quantity)
                setcouter(couter + 1)
            }
        })
    }, [listProducts])
    return (
        <div><FaListAlt onClick={toggleModal} />
            {modal && (
                <div className="modal">
                    <div onClick={toggleModal} className="overlay"></div>
                    <div className="modal-content-order">
                        <Row>
                            <div className='detail-wrapper'>
                                <div className='detail-header'>
                                    <Row className='header-conten'>
                                        <h4>
                                            Detail Order {idDelivery}
                                        </h4>
                                    </Row>
                                    <Row>
                                        <Col lg={6}>
                                            <div className='detail-bottom'>
                                                <ul>
                                                    <li>
                                                        <span>Issue Date: </span>
                                                        <h6>{createdAt}</h6>
                                                    </li>
                                                </ul>

                                                <ul>
                                                    <li>
                                                        <span>Address: </span>
                                                        <h6>{address}</h6>
                                                    </li>
                                                </ul>
                                            </div>
                                        </Col>
                                        <Col lg={6}>
                                            <div className='detail-bottom'>
                                                <ul>
                                                    <li>
                                                        <span>Email: </span>
                                                        <h6>{email}</h6>
                                                    </li>
                                                </ul>
                                                <ul>
                                                    <li>
                                                        <span>Status: </span>
                                                        {deletedBy ? <h6 className='Cancelled'>Cancelled</h6> : state === 0 ? <h6 className='Pending'>Pending</h6> : state === 1 ? <h6 className='Confirmed'>Confirm</h6> : <h6 className='Completed'>Completed</h6>}
                                                    </li>
                                                </ul>
                                            </div>
                                        </Col>
                                    </Row>
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
                                            {listProducts.map((product) => {
                                                console.log(product)
                                                return (
                                                    <tr key={product.id} className="align-middle">
                                                        <th scope='col' className='img_product_order'><img src={product.img} alt="img" /></th>
                                                        <th scope='col'>{product.name}</th>
                                                        <th scope='col'>{product.price}</th>
                                                        <th scope='col'>{product.quantity}</th>
                                                        <th scope='col'>{product.price * product.quantity}</th>
                                                    </tr>
                                                )
                                            })}
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colSpan={2}></td>
                                                <td colSpan={1}></td>
                                                <td className='font-bold text-dark' colSpan={2}>Total</td>
                                                <td className='font-bold text-theme'>{totalPriceCart}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <div className='detail-footer text-right'>
                                        {deletedBy ? "" : state === 2 ? "" : <p>Which state would you like to change?</p>}
                                        <div className='buttons'>
                                            {deletedBy ? '' : state === 0 ? <button className='theme-btn-one btn-blue-overlay btn_sm' onClick={handleState}>Confirm</button> : state === 1 ? <button className='theme-btn-one btn-blue-overlay btn_sm' onClick={handleState}>Complete</button> : ""}

                                            {deletedBy ? "" : state === 2 ? '' : <button className='theme-btn-one btn-red-overlay btn_sm ml-2' onClick={handleCancel}>Cancel</button>}
                                        </div>
                                    </div>
                                    <button className="close close-modal" onClick={toggleModal}><FaTimes /></button>

                                </div>
                            </div>
                        </Row>
                    </div >
                </div >)}
        </div >
    )
}

export default ActionOrder