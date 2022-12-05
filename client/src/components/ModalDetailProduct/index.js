import styles from '../HotProduct/ProductWrapper/ProductWrapper.module.css'
import { FaExpand, FaRegCheckCircle, FaTimesCircle, FaMinus, FaPlus } from "react-icons/fa";
import { useState } from 'react';
import axios from 'axios';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { formatter } from '../../utils/utils';
import Cookies from 'js-cookie';
import { useNavigate } from 'react-router-dom'

function ModalDetailProduct({ productId }) {

    const [modal, setModal] = useState(false);
    const [modal2, setModal2] = useState(false);
    const [message, setMessage] = useState('');
    const [success, setSuccess] = useState('');
    const [product, setProduct] = useState('');
    const [quantity, setQuantity] = useState(1);
    const navigate = useNavigate();

    const toggleModal = () => {
        axios.get(`http://127.0.0.1:8000/api/products/${productId}`)
            .then(response => {
                if (response.data.success) {
                    setProduct(response.data.data);
                }
            })
            .catch(err => {
                console.log(err);
            })
        setModal(!modal);
    }

    const closeModal = () => {
        setModal(!modal);
    }

    const closeModal2 = () => {
        setModal2(!modal2);
    }

    const handlePlus = () => {
        if (quantity < 5) {
            setQuantity(quantity + 1)
        } else {
            setQuantity(5)
        }
    }

    const handleMinus = () => {
        if (quantity > 1) {
            setQuantity(quantity - 1)
        } else {
            setQuantity(1)
        }
    }

    const handleAddToCart = () => {
        const payload = {
            productId: productId,
            quantity: quantity
        }

        axios
            .get(`http://localhost:8000/api/retrieveToken`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                if (!response.data.success) {
                    document.body.classList.remove('active-modal')
                    navigate('/login')
                } else {
                    axios
                        .post(`http://localhost:8000/api/user/cart/add`, payload, {
                            headers: {
                                Authorization: `Bearer ${Cookies.get('token')}`,
                            },
                        })
                        .then(response => {
                            if (response.data.success) {
                                setMessage(response.data.message)
                                setSuccess(response.data.success)
                                setModal(!modal);
                                setModal2(!modal2);
                            }
                        })
                        .catch(error => {
                            console.log(error);
                        });
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    if (modal) {
        document.body.classList.add('active-modal')
    } else {
        document.body.classList.remove('active-modal')
    }

    if (modal2) {
        document.body.classList.add('active-modal')
    } else {
        document.body.classList.remove('active-modal')
    }

    return (
        <>
            <a className={`${styles.action}`} title="Quickview" onClick={toggleModal}>
                <FaExpand />
            </a>

            {modal && (
                <div className="modal">
                    <div onClick={closeModal} className="overlay"></div>
                    <div className='modal-quickview'>
                        <Row>
                            <Col lg={5} md={6} sm={12} xs={12}>
                                <div>
                                    <img src={product.img} alt={product.name} />
                                </div>
                            </Col>
                            <Col lg={7} md={6} sm={12} xs={12}>
                                <div className='modal_product_content'>
                                    <h3>{product.name}</h3>
                                    <h4>{formatter.format(product.price * ((100 - product.percentSale) / 100))} <del>{formatter.format(product.price)}</del>
                                    </h4>
                                    <p>{product.description}</p>
                                </div>
                                <div className='product_count'>
                                    <div className='plus-minus-input'>
                                        <div className='input-group-button' onClick={handleMinus}>
                                            <button type="button"><FaMinus size={12} /></button>
                                        </div>
                                        <input type="number" name="quantity" value={quantity} readOnly min={1} max={5} />
                                        <div className='input-group-button' onClick={handlePlus}>
                                            <button type="button"><FaPlus size={12} /></button>
                                        </div>
                                    </div>
                                    <button type="button" className='theme-btn-one btn-black-overlay btn_sm mt-4' onClick={handleAddToCart}>THÊM VÀO GIỎ HÀNG</button>
                                </div>
                            </Col>
                        </Row>
                    </div>
                </div>
            )}

            {modal2 && (
                <div className="modal">
                    <div onClick={closeModal2} className="overlay"></div>
                    <div className="modal-content">
                        <div>
                            {success === true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                        </div>
                        <h2 className="title_modal">Thêm vào giỏ hàng {success ? 'thành công' : 'thất bại'}</h2>
                        <p className='p_modal'>{message}</p>
                        <div className='divClose'>
                            <button className="close close-modal" onClick={closeModal2}>OK</button>
                        </div>
                    </div>
                </div>
            )}
        </>
    )
}

export default ModalDetailProduct