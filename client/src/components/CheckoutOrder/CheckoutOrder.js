import React, { useState, useEffect } from 'react'
import { useForm } from "react-hook-form";
import { useNavigate } from "react-router-dom";
import { FaRegCheckCircle, FaTimesCircle } from 'react-icons/fa'
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row';
import axios from "axios";
import Cookies from 'js-cookie'
import "./CheckoutOrder.css"
import AccountEditModal from '../AccountEditArea/AccountEditModal';
import { formatter } from '../../utils/utils';
import CartArea from '../CartArea';
import EmptyCart from '../CartArea/EmptyCart';

const CheckoutOrder = () => {
    const [listProduct, setListProduct] = useState([]);
    const [message, setMessage] = useState("")
    const [success, setSuccess] = useState("")
    const [percent, setpercent] = useState(0)
    const [voucherId, setvoucherId] = useState("")
    const [nameReceiver, setnameReceiver] = useState('')
    const [phoneReceiver, setphoneReceiver] = useState('')
    const [totalPriceCart, settotalPriceCart] = useState(0)
    const [district, setdistrict] = useState('')
    const [province, setprovince] = useState('')
    const [ward, setWard] = useState('')
    const [street, setStreet] = useState('')
    const navigate = useNavigate();
    const [check, setCheck] = useState(0);
    const [modal, setModal] = useState(false);
    const { register, handleSubmit, formState: { errors } } = useForm();
    const { register: register2, handleSubmit: handleSubmit2 } = useForm();

    useEffect(() => {
        axios
            .get(`http://127.0.0.1:8000/api/user/cart/state=all`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                setListProduct(response.data.data);
                setCheck(1);
            })
            .catch(function (error) {
                console.log(error);
            });
    }, []);
    const closeModal = () => {
        setModal(!modal);
    }

    const PlaceOrder = () => {

        const payload = {
            phoneReceiver: phoneReceiver,
            nameReceiver: nameReceiver,
            voucherCode: voucherId,
            address: street + ', ' + ward + ', ' + district + ', ' + province,
            paidType: 0
        }
        console.log(payload)
        axios
            .post(`http://127.0.0.1:8000/api/user/order/placeorder`, payload, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                console.log(response.data)
                setMessage(response.data.message)
                setSuccess(response.data.success)
                setModal(!modal)
                if (response.data.success) {
                    navigate("/order-completed")
                } else {
                    setModal(!modal)
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    const [couter, setcouter] = useState(0)
    useEffect(() => {
        if (listProduct) {
            listProduct.map((product) => {
                if (couter < 1) {
                    settotalPriceCart(totalPriceCart => totalPriceCart + (product.price * ((100 - product.percentSale) / 100)) * product.quantity)
                    setcouter(couter + 1)
                }
            })
        }
    }, [listProduct])

    const toggleModal = () => {
        setTimeout(() => { setModal(!modal); }, 1000)
    };
    const onSubmit2 = (data) => {
        axios
            .post(`http://127.0.0.1:8000/api/user/voucherCheck`, data, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                console.log(response.data)
                setMessage(response.data.message)
                setSuccess(response.data.success)
                if (response.data.success) {
                    setvoucherId(response.data.data.name)
                    setpercent(response.data.data.percent)
                }
            })
    }
    console.log(voucherId)
    return (
        <>
            {!listProduct && check > 0 && <EmptyCart />}
            {listProduct && check > 0 &&
                <section id='checkout' className='ptb-100'>
                    <div className='container'>
                        <Row>
                            <Col lg={6} md={12} sm={12} xs={12}>
                                <div className='checkout-area-bg bg-white'>
                                    <div className='check-heading'>
                                        <h3>Thông tin đơn hàng</h3>
                                    </div>
                                    <div className='check-out-form'>
                                        <form onSubmit={handleSubmit(PlaceOrder)}>
                                            <Row>
                                                <Col lg={12} md={12} sm={12} xs={12}>
                                                    <div className='form-group'>
                                                        <label htmlFor="nameReceiver">Tên người nhận<span className="text-danger">*</span></label>
                                                        <input
                                                            type="text"
                                                            value={nameReceiver}
                                                            className='form-control'
                                                            placeholder='Tên người nhận'
                                                            {...register("nameReceiver", { required: true, onChange: (e) => { setnameReceiver(e.target.value) } })} />
                                                    </div>
                                                </Col>

                                                <Col lg={12} md={12} sm={12} xs={12}>
                                                    <div className='form-group'>
                                                        <label htmlFor="phoneReceiver">Số điện thoại<span className="text-danger">*</span></label>
                                                        <input
                                                            type="text"
                                                            value={phoneReceiver}
                                                            className='form-control'
                                                            placeholder='Số điện thoại'
                                                            {...register("phoneReceiver", { required: true, onChange: (e) => { setphoneReceiver(e.target.value) } })} />
                                                    </div>
                                                </Col>
                                                <Col lg={6} md={12} sm={12} xs={12}>
                                                    <div className='form-group'>
                                                        <label htmlFor="province">Tỉnh/Thành Phố<span className="text-danger">*</span></label>
                                                        <input
                                                            type="text"
                                                            value={province}
                                                            className='form-control'
                                                            placeholder='Tỉnh/Thành Phố'
                                                            {...register("province", { required: true, onChange: (e) => { setprovince(e.target.value) } })} />
                                                    </div>
                                                </Col>
                                                <Col lg={6} md={12} sm={12} xs={12}>
                                                    <div className='form-group'>
                                                        <label htmlFor="district">Quận/Huyện<span className="text-danger">*</span></label>
                                                        <input
                                                            type="text"
                                                            value={district}
                                                            className='form-control'
                                                            placeholder='Quận/Huyện'
                                                            {...register("district", { required: true, onChange: (e) => { setdistrict(e.target.value) } })} />
                                                    </div>
                                                </Col>
                                                <Col lg={6} md={12} sm={12} xs={12}>
                                                    <div className='form-group'>
                                                        <label htmlFor="ward">Xã/Phường<span className="text-danger">*</span></label>
                                                        <input
                                                            type="text"
                                                            value={ward}
                                                            className='form-control'
                                                            placeholder='Xã/Phường'
                                                            {...register("ward", { required: true, onChange: (e) => { setWard(e.target.value) } })} />
                                                    </div>
                                                </Col>
                                                <Col lg={12} md={12} sm={12} xs={12}>
                                                    <div className='form-group'>
                                                        <label htmlFor="street">Đường<span className="text-danger">*</span></label>
                                                        <input
                                                            type="text"
                                                            value={street}
                                                            className='form-control'
                                                            placeholder='VD: 36 Nguyễn Chí Thanh '
                                                            {...register("street", { required: true, onChange: (e) => { setStreet(e.target.value) } })} />
                                                    </div>
                                                </Col>
                                            </Row>
                                        </form>
                                    </div>
                                </div>
                            </Col>
                            <Col lg={6} md={12} sm={12} xs={12}>
                                <div className='order_review  bg-white'>
                                    <div className='order_review bg-white'>
                                        <div className='check-heading'>
                                            <h3>Áp dụng mã giảm giá</h3>
                                        </div>
                                        <div className='coupon'>
                                            <form onSubmit={handleSubmit2(onSubmit2)}>
                                                <input
                                                    type="text"
                                                    placeholder="Mã giảm giá"
                                                    {...register2("voucherCode")}
                                                />
                                                <AccountEditModal message={message} success={success} nameBtn='Áp Dụng' />
                                            </form>
                                        </div>
                                    </div>
                                    <div className='check-heading'>
                                        <h3>Đơn hàng của bạn</h3>
                                    </div>
                                    <div className='table-responsive order_table'>
                                        <table className='table'>
                                            <thead>
                                                <tr>
                                                    <th>Sản phẩm</th>
                                                    <th>Giá</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {listProduct && listProduct.map((product, index) => {
                                                    return (
                                                        <tr key={index}>
                                                            <td>{product.name}<span className="product-qty"> X {product.quantity}</span></td>
                                                            <td>{formatter.format((product.price * ((100 - product.percentSale) / 100)) * product.quantity)}</td>
                                                        </tr>)
                                                })}
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Tạm tính:</th>
                                                    <td className='product-subtotal'>{formatter.format(totalPriceCart)}</td>
                                                </tr>
                                                <tr>
                                                    <th>Giảm giá:</th>
                                                    <td >{percent}% </td>
                                                </tr>
                                                <tr>
                                                    <th>Thành tiền:</th>
                                                    <td className='product-subtotal'>{formatter.format(totalPriceCart - (percent * totalPriceCart / 100))} </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div className='coupon' onClick={PlaceOrder}>
                                        <button type="submit" className="theme-btn-one bg-black btn_sm" >Hoàn tất đặt hàng</button>
                                        {modal && (
                                            <div className="modal">
                                                <div onClick={toggleModal} className="overlay"></div>
                                                <div className="modal-content">
                                                    <div>
                                                        {success == true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                                                    </div>
                                                    <h2 className="title_modal">Đặt hàng {success ? 'thành công' : 'thất bại'}</h2>
                                                    <p className='p_modal'>{message}</p>
                                                    <div className='divClose'>
                                                        <button className="close close-modal" onClick={closeModal}>OK</button>
                                                    </div>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </Col>
                        </Row>
                    </div>
                </section>
            }
        </>
    )
}

export default CheckoutOrder