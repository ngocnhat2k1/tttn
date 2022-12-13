import React, { useEffect, useState } from 'react'
import styles from './ListProduct.module.scss'
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row';
import axios from 'axios';
import Cookies from 'js-cookie';
import { Link } from 'react-router-dom'
import { FaRegCheckCircle, FaTimesCircle } from 'react-icons/fa'
import { useNavigate } from "react-router-dom";
import { FaRegHeart, FaExpand } from "react-icons/fa";
import { formatter } from '../../../utils/utils'


function ListProduct({ currentItems }) {
    const [message, setMessage] = useState("")
    const [success, setSuccess] = useState("")
    const navigate = useNavigate();

    const AddToCart = (productId) => {
        const payload = {
            productId: productId,
            quantity: 1
        }
        axios
            .put(`http://127.0.0.1:8000/api/user/cart/update`, payload, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                if (!response.data.success) {
                    navigate("/login")
                } else {
                    setMessage(response.data.message)
                    setSuccess(response.data.success)
                    setModal(!modal)
                }
            })

    }
    const [modal, setModal] = useState(false);
    const AddWishlist = (productId) => {
        axios
            .post(`http://127.0.0.1:8000/api/user/favorite/${productId}`, [], {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                setMessage(response.data.message)
                setSuccess(response.data.success)
                setModal(!modal)
            })
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
        <Row>
            {currentItems && Object.values(currentItems).map((product) => {
                return (
                    <Col lg={4} md={4} sm={6} xs={12} key={product.id}>
                        <div className={styles.productWrapper}>
                            <div className={styles.thumb}>
                                <Link to={`/shop/${product.id}`} className={styles.image}>
                                    <img src={product.img} alt={product.name} />
                                </Link>
                                <span className={styles.badges}>
                                    {product.percentSale !== 0 ? <span className={product.percentSale !== "" ? styles.sale : ""}>
                                        {product.percentSale + "% OFF"}</span>
                                        : ''}
                                </span>
                                <div className={styles.actions}>
                                    <a onClick={() => AddWishlist(product.id)} className={`${styles.wishList} ${styles.action}`} title="Wishlist" >
                                        <FaRegHeart />
                                    </a>
                                    <a href="" className={`${styles.quickView} ${styles.action}`} title="Quickview">
                                        <FaExpand />
                                    </a>
                                </div>
                                <div onClick={() => { AddToCart(product.id) }}><button className={`${styles.addToCart}`}>Thêm vào giỏ hàng</button></div>

                            </div>
                            <div className={styles.content}>
                                <h5 className={styles.title}>
                                    <a href="">{product.name}</a>
                                </h5>
                                <span className={styles.price}>
                                    {product.percentSale !== "" ? formatter.format(product.price * ((100 - product.percentSale) / 100)) : formatter.format(product.price)}
                                </span>
                            </div>
                            {modal && (
                                <div className="modal">
                                    <div className="overlay"></div>
                                    <div className="modal-content">
                                        <div>
                                            {success == true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                                        </div>
                                        <h2 className="title_modal">{product.name} {success ? 'Successful' : 'Failed'}</h2>
                                        <p className='p_modal'>{message}</p>
                                        <div className='divClose'>
                                            <button className="close close-modal" onClick={closeModal}>OK</button>
                                        </div>

                                    </div>
                                </div>
                            )}
                        </div>
                    </Col>

                )
            })}
        </Row>)
}
export default ListProduct