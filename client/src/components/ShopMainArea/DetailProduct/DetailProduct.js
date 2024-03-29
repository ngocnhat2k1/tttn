import React, { useEffect, useState } from 'react'
import Cookies from 'js-cookie';
import { FaMinus, FaPlus, FaHeart, FaTimesCircle, FaRegCheckCircle } from "react-icons/fa"
import axios from 'axios';
import { useForm } from "react-hook-form";
import 'bootstrap/dist/css/bootstrap.min.css';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import ModalATag from '../../ModalATag/index'
import { useNavigate } from "react-router-dom";
import { useParams } from "react-router-dom"
import CommonBanner from '../../CommonBanner';
import "./DetailProduct.css"
import AccountEditModal from "../../AccountEditArea/AccountEditModal/index"
import { formatter } from '../../../utils/utils';

const DetailProduct = () => {
    const [isLogin, setIsLogin] = useState(true)
    const { productId } = useParams()
    const [productImg, setProductImg] = useState('')
    const [productName, setProductName] = useState('')
    const [productPrice, setProductPrice] = useState('')
    const [productDescription, setProductDescription] = useState('')
    const [percentSale, setPercentSale] = useState(0)
    const [quantityPurchased, setQuantityPurchased] = useState(1)
    const [message, setMessage] = useState("")
    const [success, setSuccess] = useState(true)
    const [listReview, setListReview] = useState([])
    const [comment, setComment] = useState('')
    const navigate = useNavigate();
    const [activeTab, setActiveTab] = useState('description')
    const { register, handleSubmit, watch, formState: { errors } } = useForm();
    const { register: register2, handleSubmit: handleSubmit2 } = useForm();
    const [modal, setModal] = useState(false);

    const toggleModal = () => {
        setTimeout(() => { setModal(!modal); }, 1)
    };

    const closeModal = () => {
        setModal(!modal);
        if (!isLogin) {
            navigate("/login")
        }
    }

    if (modal) {
        document.body.classList.add('active-modal')
    } else {
        document.body.classList.remove('active-modal')
    }
    useEffect(() => {
        axios
            .get(`http://127.0.0.1:8000/api/products/${productId}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                if (response.data.success) {
                    setProductImg(response.data.data.img)
                    setProductName(response.data.data.name)
                    setProductPrice(response.data.data.price)
                    setProductDescription(response.data.data.description)
                    setPercentSale(response.data.data.percentSale)
                }
            })
            .catch((errors) => {
                console.log(errors)
            })
    }, [])
    const AddWishlist = () => {
        axios
            .get(`http://localhost:8000/api/retrieveToken`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                if (response.data.success) {
                    axios
                        .post(`http://127.0.0.1:8000/api/user/favorite/${productId}`, [], {
                            headers: {
                                Authorization: `Bearer ${Cookies.get('token')}`,
                            },
                        })
                        .then((response) => {
                            setMessage(response.data.message)
                            setSuccess(response.data.success)
                        })
                } else {
                    navigate("/login")
                }
            })
    }
    useEffect(() => {
        axios
            .get(`http://127.0.0.1:8000/api/feedback/product/${productId}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                setListReview(response.data.data)
            })

    }, [])

    const AddToCart = () => {
        const payload = {
            productId: productId,
            quantity: quantityPurchased
        }
        console.log(payload)
        axios
            .get(`http://localhost:8000/api/retrieveToken`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                if (response.data.success) {
                    axios
                        .put(`http://127.0.0.1:8000/api/user/cart/update`, payload, {
                            headers: {
                                Authorization: `Bearer ${Cookies.get('token')}`,
                            },
                        })
                        .then((response) => {
                            setMessage(response.data.message)
                            setSuccess(response.data.success)
                            setModal(!modal)
                        })
                } else {
                    navigate("/login")
                }
            })
    }

    const sentReview = (data) => {
        const payload = {
            ...data,
            productId: productId
        }
        axios
            .get(`http://localhost:8000/api/retrieveToken`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                if (response.data.success) {
                    axios
                        .post(`http://127.0.0.1:8000/api/user/feedback/create`, payload, {
                            headers: {
                                Authorization: `Bearer ${Cookies.get('token')}`,
                            },
                        })
                        .then((response) => {
                            setMessage(response.data.message)
                            setSuccess(response.data.success)
                        })
                } else {
                    navigate("/login")
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }
    return (
        <>
            <CommonBanner namePage="Chi tiết sản phẩm"></CommonBanner>
            <section id='product_single_one' className='ptb-100'>
                <div className='container'>
                    <div className='row area_boxed'>
                        <Col lg={4}>
                            <div className='product_single_one_img'>
                                <img src={productImg} alt="img" />
                            </div>
                        </Col>
                        <Col lg={8} className='text-left'>
                            <div className='product_details_right_one'>
                                <div className='modal_product_content_one'>
                                    <h3>{productName}</h3>
                                    {percentSale === 0 ? <h4>{formatter.format(productPrice)}</h4> : <h4>{formatter.format(productPrice - (percentSale * productPrice / 100))}<del>{formatter.format(productPrice)}</del> </h4>}
                                    <p>{productDescription}</p>
                                    <form id='product_count_form_two'>
                                        <div className='product_count_one'>
                                            <div className='plus-minus-input'>
                                                <div className='input-group-button'>
                                                    <button type="button" className='button' onClick={() => { quantityPurchased > 1 ? setQuantityPurchased(quantityPurchased - 1) : setQuantityPurchased(1) }}><FaMinus></FaMinus></button>
                                                </div>
                                                <input type="number" className='form-control' readOnly value={quantityPurchased}
                                                    {...register("quantity", { required: true })} />
                                                <div className='input-group-button'>
                                                    <button type="button" className='button' onClick={() => { quantityPurchased === 7 ? setQuantityPurchased(7) : setQuantityPurchased(quantityPurchased + 1) }}><FaPlus></FaPlus></button>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                    <div className='links_Product_areas'>
                                        <ul>
                                            <li onClick={AddWishlist}>
                                                <ModalATag message={message} success={success} nameBtn='Thêm vào danh sách yêu thích' icon={<FaHeart />}></ModalATag>
                                            </li>
                                        </ul>
                                        <button htmlFor='submit-form' className="theme-btn-one bg-black btn_sm" onClick={AddToCart}>
                                            Thêm vào giỏ hàng
                                        </button>
                                        {modal && (
                                            <div className="modal">
                                                <div onClick={toggleModal} className="overlay"></div>
                                                <div className="modal-content">
                                                    <div>
                                                        {success == true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                                                    </div>
                                                    <h2 className="title_modal">Thêm vào giỏ hàng {success ? 'Thành Công' : 'Thất Bại'}</h2>
                                                    <p >{message}</p>
                                                    <div className='divClose'>
                                                        <button className="close close-modal" onClick={closeModal}>OK</button>
                                                    </div>

                                                </div>
                                            </div>)}
                                    </div>
                                </div>
                            </div>
                        </Col>
                    </div>
                    <Row>
                        <Col lg={12}>
                            <div className='product_details_tabs text-left'>
                                <ul className='nav nav-tabs'>
                                    <li onClick={() => { setActiveTab('description') }}>
                                        <a href="#description" data-toggle="tab" onClick={(e) => { e.preventDefault(); }} className={activeTab === 'description' ? "active" : ''}>Mô tả</a>
                                    </li>
                                    <li onClick={() => { setActiveTab('review') }}>
                                        <a href="#review" data-toggle="tab" onClick={(e) => { e.preventDefault(); }} className={activeTab === 'review' ? "active" : ''}>Đánh giá</a>
                                    </li>
                                </ul>
                                <div className='tab-content'>
                                    <div id='description' className={activeTab === 'description' ? "tab-pane fade in active show" : 'tab-pane fade'}>
                                        <div className='product_description'>
                                            <p>{productDescription}</p>
                                        </div>
                                    </div>
                                    <div id='review' className={activeTab === 'review' ? "tab-pane fade in active show" : 'tab-pane fade'}>
                                        <div className='product_reviews'>
                                            {listReview && listReview.map((review, index) => {
                                                return (
                                                    <ul key={index}>
                                                        <li className='media'>
                                                            <div className='media-img'>
                                                                {review.avatar ? <img src={review.avatar} alt="img" /> : <img src={review.defaultAvatar} alt="img" />}
                                                            </div>
                                                            <div className='media-body'>
                                                                <div className='media-header'>
                                                                    <div className='media-name'>
                                                                        <h4>{review.firstName} {review.lastName}</h4>
                                                                    </div>
                                                                    <div className='post-share'>
                                                                        <p className=''>{review.createdAt}</p>
                                                                    </div>
                                                                </div>
                                                                <div className='media-pragraph'>
                                                                    <p>{review.comment}</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                )
                                            })}
                                        </div>
                                        <div className='input-review'>
                                            <Row>
                                                <form onSubmit={handleSubmit2(sentReview)}>
                                                    <Col lg={12} md={12} sm={12} xs={12}>
                                                        <label htmlFor="comment" className='media-name'>Để lại đánh giá</label>
                                                        <input type="text"
                                                            className='form-control comment-input'
                                                            value={comment}
                                                            placeholder='Để lại đánh giá của bạn về sản phẩm..'
                                                            {...register2('comment', { required: true, onChange: (e) => { setComment(e.target.value) } })} />
                                                        {errors.comment && errors.comment.type === 'required' && <span className='error'>Để lại bình luận của bạn trước khi gửi</span>}
                                                    </Col>
                                                    <div className='submit-comment'>
                                                        <AccountEditModal message={message} success={success} nameBtn='Gửi đánh giá' />
                                                    </div>
                                                </form>
                                            </Row>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Col>
                    </Row>
                </div>

            </section>
        </>
    )
}

export default DetailProduct