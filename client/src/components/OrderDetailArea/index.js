import styles from './OrderDetailArea.module.css'
import { FaArrowLeft } from 'react-icons/fa'
import { useEffect, useState } from 'react'
import axios from 'axios'
import Cookies from 'js-cookie'
import { useParams, Link } from "react-router-dom"
import { FaQuestionCircle, FaTimesCircle, FaRegCheckCircle } from "react-icons/fa"
import { formatter } from '../../utils/utils'
import NotFoundOrder from '../NotFoundOrder'

function OrderDetailArea() {

    const { id } = useParams();
    const [modal, setModal] = useState(false);
    const [modal2, setModal2] = useState(false);
    const [modal3, setModal3] = useState(false);
    const [message, setMessage] = useState('');
    const [success, setSuccess] = useState('');
    const [order, setOrder] = useState('')
    const [check, setCheck] = useState('');
    const [totalProduct, setTotalProduct] = useState(0);
    const [couter, setCouter] = useState(0);

    const toggleModal = () => {
        setModal(!modal);
    }

    const closeModal = () => {
        setModal(!modal);
    }

    const closeModal2 = () => {
        if (success) {
            setModal2(!modal2);
            window.location.reload(false);
        } else {
            setModal2(!modal2);
        }
    }

    const closeModal3 = () => {
        if (success) {
            setModal3(!modal3);
            window.location.reload(false);
        } else {
            setModal3(!modal3);
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

    if (modal3) {
        document.body.classList.add('active-modal')
    } else {
        document.body.classList.remove('active-modal')
    }

    useEffect(() => {
        axios
            .get(`http://127.0.0.1:8000/api/user/order/idDelivery/${id}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then(response => {
                if (response.data.success) {
                    setOrder(response.data.data);
                    setCheck(1);
                } else {
                    setCheck(0);
                }
            })
            .catch(error => {
                console.log(error);
            });
    }, [])

    useEffect(() => {
        if (order.products) {
            order.products.map((product) => {
                if (couter < 1) {
                    setTotalProduct(totalProduct => totalProduct + (product.price * ((100 - product.percentSale) / 100)) * product.quantity)
                    setCouter(couter + 1)
                }
            })
        }
    }, [order])

    const handleComplete = (productId) => {
        axios
            .put(`http://127.0.0.1:8000/api/user/order/${productId}/status`, [], {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                }
            })
            .then(response => {
                if (response.data.success) {
                    setMessage(response.data.message);
                } else {
                    setMessage(response.data.errors)
                }
                setSuccess(response.data.success);
                setModal3(!modal3);
            })
            .catch(err => {
                console.log(err);
            })
    }

    const handleCancel = () => {
        axios
            .delete(`http://127.0.0.1:8000/api/user/order/${id}/cancel`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                setMessage(response.data.message)
                setSuccess(response.data.success)
                setModal(!modal);
                setModal2(!modal2);
            })
            .catch(err => {
                console.log(err)
            })
    }

    console.log(order)

    return (
        <>
            {
                check === 0 && <NotFoundOrder id={id} />
            }
            {check === 1 &&
                <div className={styles.detailArea}>
                    <div className={styles.backBtn}>
                        <Link to="/my-account/customer-order" className='theme-btn-one btn-black-overlay btn_sm'>
                            <FaArrowLeft size={12} /> TR??? V???
                        </Link>
                    </div>
                    <table align='center' border={0} cellPadding={0} cellSpacing={0} className={styles.boxTable}>
                        <tbody>
                            <tr>
                                <td>
                                    <table align='center' border={0} cellPadding={0} cellSpacing={0}>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <img src="/Logo.png" alt="Logo" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div className={order.order.status === "????n h??ng ???? b??? h???y" ? 'colorFail' : 'colorSuccess'}>
                                                        {order.order.status === "????n h??ng ???? b??? h???y" ? <FaTimesCircle size={70} /> : <FaRegCheckCircle size={70} />}
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h2 className={styles.title}>{order.order.status}</h2>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p>{order.order.status === "????n h??ng ???? b??? h???y" ? "H??y ti???p t???c mua h??ng ??? Shop H?????ng D????ng b???n nh??!" : "C???m ??n b???n ???? mua h??ng t???i Shop H?????ng D????ng"}</p>
                                                    <p>M?? ????n h??ng: {order.order.idDelivery}</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table border={0} cellPadding={0} cellSpacing={0} className="mt-4">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <h2 className={styles.title}>CHI TI???T ????N H??NG C???A B???N</h2>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table className={styles.orderDetail} border={0} cellPadding={0} cellSpacing={0} align='left'>
                                        <tbody>
                                            <tr align='left'>
                                                <th className={styles.thImg}>H??NH ???NH</th>
                                                <th className={styles.thName}>S???N PH???M</th>
                                                <th>S??? L?????NG</th>
                                                <th>GI???M GI??</th>
                                                <th className={styles.thPrice}>GI?? TI???N</th>
                                            </tr>
                                            {order.products.map((product) => {
                                                return (
                                                    <tr key={product.id}>
                                                        <td>
                                                            <img src={product.img} alt="" />
                                                        </td>
                                                        <td valign='top'>
                                                            <h5>{product.name}</h5>
                                                        </td>
                                                        <td valign='top'>
                                                            <h5>{product.quantity}</h5>
                                                        </td>
                                                        <td valign='top'>
                                                            <h5>{product.percentSale}%</h5>
                                                        </td>
                                                        <td valign='top'>
                                                            <h5 className={styles.price}>{formatter.format(product.price)}</h5>
                                                        </td>
                                                    </tr>
                                                )
                                            })}
                                            <tr>
                                                <td colSpan={2} className={styles.totalProduct}>T???m t??nh:</td>
                                                <td colSpan={3} className={styles.priceTotalProduct}>
                                                    <b>{formatter.format(totalProduct)}</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colSpan={2} className={styles.totalProduct}>M?? khuy???n m??i:</td>
                                                <td colSpan={3} className={styles.priceTotalProduct}>
                                                    <b>- {order.voucher?.percent ? order.voucher.percent : 0}%</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colSpan={2} className={styles.totalProduct}>T???ng ti???n:</td>
                                                <td colSpan={3} className={styles.priceTotalProduct}>
                                                    <b>{formatter.format(order.order.totalPrice)}</b>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table className={styles.tableAddress} cellPadding={0} cellSpacing={0} border={0} align='left'>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <h5>N??I GIAO H??NG</h5>
                                                    <p>220, khu ph??? 2, ???????ng Nguy???n H???u Th???, huy???n B???n L???c, t???nh Long An</p>
                                                </td>
                                                <td>
                                                    <h5>N??I NH???N H??NG</h5>
                                                    <p>{order.order.address}</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                        <table className={styles.tableAction} align='center' border={0} cellPadding={0} cellSpacing={0} width="100%">
                                            <tbody>
                                                <tr>
                                                    {order && <td>
                                                        {order.order.status === "????n h??ng ???????c giao th??nh c??ng." ? <button type="button" className='theme-btn-one btn-blue-overlay btn_sm' onClick={() => handleComplete(order.order.id)}>???? NH???N H??NG</button> : <button type="button" className='btn btn-lg theme-btn-one btn-blue-overlay btn_sm btn-secondary mx-3' disabled={true} onClick={() => handleComplete(order.order.id)}>???? NH???N H??NG</button>}
                                                        {(order.order.status === "????n h??ng ??ang ch??? x??? l??." || order.order.status === "????n h??ng ??ang ch??? thanh to??n.") && <button type="button" className='theme-btn-one btn-red-overlay btn_sm ml-2 mx-3' onClick={toggleModal}>H???Y ????N H??NG</button>}
                                                    </td>}

                                                </tr>
                                            </tbody>
                                        </table>

                                    {modal && (
                                        <div className="modal">
                                            <div onClick={closeModal} className="overlay"></div>
                                            <div className="modal-content">
                                                <div>
                                                    <FaQuestionCircle className='svgQuestion' size={90} />
                                                </div>
                                                <h2 className="title_modal">B???n ch???c ch???n mu???n h???y ????n h??ng n??y?</h2>
                                                <div className='divClose'>
                                                    <button className="close close-modal btnNo" onClick={closeModal}>Kh??ng</button>
                                                    <button className="close close-modal btnYes" onClick={handleCancel}>C??</button>
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
                                                <h2 className="title_modal">H???y ????n h??ng {success ? "th??nh c??ng" : "th???t b???i"}</h2>
                                                <p className='p_modal'>{message}</p>
                                                <div className='divClose'>
                                                    <button className="close close-modal" onClick={closeModal2}>OK</button>
                                                </div>
                                            </div>
                                        </div>
                                    )}

                                    {modal3 && (
                                        <div className="modal">
                                            <div onClick={closeModal3} className="overlay"></div>
                                            <div className="modal-content">
                                                <div>
                                                    {success === true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                                                </div>
                                                <h2 className="title_modal">X??c nh???n nh???n h??ng {success ? "th??nh c??ng" : "th???t b???i"}</h2>
                                                <p className='p_modal'>{message}</p>
                                                <div className='divClose'>
                                                    <button className="close close-modal" onClick={closeModal3}>OK</button>
                                                </div>
                                            </div>
                                        </div>
                                    )}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            }
        </>
    )
}

export default OrderDetailArea