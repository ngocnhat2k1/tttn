import styles from './Cart.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { Link } from 'react-router-dom';
import { useState, useEffect } from 'react';
import EmptyCart from './EmptyCart';
import axios from "axios";
import Cookies from 'js-cookie'
import { FaQuestionCircle, FaRegCheckCircle, FaTimesCircle, FaTrashAlt, FaMinus, FaPlus } from 'react-icons/fa';
import { formatter } from '../../utils/utils';

function CartArea() {
    const [loader, setLoader] = useState(true)
    const [listProduct, setListProduct] = useState([]);
    const [totalPriceCart, settotalPriceCart] = useState(0);
    const [couter, setcouter] = useState(0)
    const [check, setCheck] = useState(0);
    const [message, setMessage] = useState("");
    const [success, setSuccess] = useState("");
    const [modal, setModal] = useState(false);
    const [modal2, setModal2] = useState(false);

    useEffect(() => {
        axios
            .get(`http://localhost:8000/api/user/cart/state=all`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then(response => {
                setListProduct(response.data.data);
                setCheck(1);
            })
            .catch(error => {
                console.log(error);
            });
    }, [loader]);

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

    const handleDeleteProduct = (id) => {
        axios
            .delete(`http://localhost:8000/api/user/cart/destroy/${id}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`
                }
            })
            .then((response) => {
                setMessage(response.data.message)
                setSuccess(response.data.success)
                setModal(!modal)
                setModal2(!modal2);
            })
    }

    const handleIncrease = (id) => {
        axios
            .post(`http://localhost:8000/api/user/cart/add/${id}`, [], {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then(response => {
                setLoader(!loader);
            })
            .catch(error => {
                console.log(error);
            });
    }

    const handleReduce = (id) => {
        axios
            .post(`http://localhost:8000/api/user/cart/reduce/${id}`, [], {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then(response => {
                setLoader(!loader);
            })
            .catch(error => {
                console.log(error);
            });
    }

    const toggleModal = () => {
        setModal(!modal);
    }

    const closeModal = () => {
        setModal(!modal);
    }

    const closeModal2 = () => {
        setModal2(!modal2);
        if (success) {
            window.location.reload(false)
        }
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
            {!listProduct && check > 0 && <EmptyCart />}
            {listProduct && check > 0 &&
                <section id="cartArea" className='ptb100'>
                    <Container>
                        <Row>
                            <Col lg={12} md={12} sm={12} xs={12}>
                                <div className={styles.tableDesc}>
                                    <div className={`${styles.tablePage} ${styles.tableResponsive}`}>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th className={styles.productThumb}>Hình ảnh</th>
                                                    <th className={styles.productName}>Sản phẩm</th>
                                                    <th className={styles.productPrice}>Giá tiền</th>
                                                    <th className={styles.productQuantity}>Số lượng</th>
                                                    <th className={styles.productTotal}>Tổng tiền</th>
                                                    <th className={styles.productRemove}>Bỏ khỏi giỏ hàng</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {listProduct.map((product, index) => {
                                                    return (
                                                        <tr key={index}>
                                                            <td className={styles.productThumb}>
                                                                <Link to={`/shop/${product.id}`}>
                                                                    <img src={product.img} alt="img" />
                                                                </Link>
                                                            </td>
                                                            <td className={styles.productName}>
                                                                <Link to={`/shop/${product.id}`}>
                                                                    {product.name}
                                                                </Link>
                                                            </td>
                                                            <td className={styles.productPrice}>
                                                                {formatter.format(product.price * ((100 - product.percentSale) / 100))}
                                                            </td>
                                                            <td className={styles.productQuantity}>
                                                                <div className={styles.plusMinusInput}>
                                                                    <div>
                                                                        <button type="button" className='button' onClick={() => handleReduce(product.id)}><FaMinus size={14} /></button>
                                                                    </div>
                                                                    <input
                                                                        type="number"
                                                                        value={product.quantity}
                                                                        min='1'
                                                                        max='10'
                                                                        readOnly
                                                                    />
                                                                    <div>
                                                                        <button type="button" className='button' onClick={() => handleIncrease(product.id)}><FaPlus size={14} /></button>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td className={styles.productTotal}>{formatter.format(product.price * ((100 - product.percentSale) / 100) * product.quantity)}</td>
                                                            <td className={styles.productRemove}><FaTrashAlt onClick={toggleModal} />
                                                                {modal && (
                                                                    <div className="modal">
                                                                        <div onClick={closeModal} className="overlay"></div>
                                                                        <div className="modal-content">
                                                                            <div>
                                                                                <FaQuestionCircle className='svgQuestion' size={90} />
                                                                            </div>
                                                                            <h2 className="title_modal">Bạn chắc chắn muốn xóa sản phẩm?</h2>
                                                                            <div className='divClose'>
                                                                                <button className="close close-modal btnNo" onClick={closeModal}>Không</button>
                                                                                <button className="close close-modal btnYes" onClick={() => handleDeleteProduct(product.id)}>Có</button>
                                                                            </div>
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
                                                                            <h2 className="title_modal">Xóa sản phẩm {success ? "thành công" : "thất bại"}</h2>
                                                                            <p className='p_modal'>{message}</p>
                                                                            <div className='divClose'>
                                                                                <button className="close close-modal" onClick={closeModal2}>OK</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                )}
                                                            </td>
                                                        </tr>
                                                    )
                                                })}
                                            </tbody>
                                        </table>
                                    </div>
                                    <div className={styles.btnClearCart}>
                                        <button type="button" className='theme-btn-one btn-black-overlay btn_sm'>Làm trống giỏ hàng</button>
                                    </div>
                                </div>
                            </Col>
                            <Col lg={12} md={12}>
                                <div className={styles.cartTotal}>
                                    <h3>Tổng tiền trong giỏ hàng</h3>
                                    <div className={styles.cartInner}>
                                        <div className={`${styles.cartSubTotal} ${styles.border}`}>
                                            <p>Tổng tiền</p>
                                            <p className={styles.cartSubTotalDetail}>{formatter.format(totalPriceCart)}</p>
                                        </div>
                                        <div className={styles.checkoutBtn}>
                                            <Link to="/checkout-order" className='theme-btn-one btn-black-overlay btn_sm'>Tiến hành thanh toán</Link>
                                        </div>
                                    </div>
                                </div>
                            </Col>
                        </Row>
                    </Container>
                </section>
            }
        </>
    )
}

export default CartArea
