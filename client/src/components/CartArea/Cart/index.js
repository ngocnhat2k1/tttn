import styles from './Cart.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { Link } from 'react-router-dom';
import { FaTrashAlt } from 'react-icons/fa';
import Balo from '../../../images/balo_tibi_3.png';
import { useState } from 'react';
import { useForm } from "react-hook-form";

function Cart() {
    const [quantity, setQuantity] = useState(1);

    const {
        register,
        handleSubmit,
        formState: { errors },
    } = useForm();

    const onSubmit = (data) => {

    }

    return (
        <section id={styles.cartArea} className='ptb100'>
            <Container>
                <Row>
                    <Col lg={12} md={12} sm={12} xs={12}>
                        <div className={styles.tableDesc}>
                            <div className={`${styles.tablePage} ${styles.tableResponsive}`}>
                                <table>
                                    <thead>
                                        <tr>
                                            <th className={styles.productThumb}>Image</th>
                                            <th className={styles.productName}>Product</th>
                                            <th className={styles.productPrice}>Price</th>
                                            <th className={styles.productQuantity}>Quantity</th>
                                            <th className={styles.productTotal}>Total</th>
                                            <th className={styles.productRemove}>Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td className={styles.productThumb}>
                                                <Link>
                                                    <img src={Balo} alt="img" />
                                                </Link>
                                            </td>
                                            <td className={styles.productName}>
                                                <Link>
                                                    Fit-Flare Dress
                                                </Link>
                                            </td>
                                            <td className={styles.productPrice}>$52.00</td>
                                            <td className={styles.productQuantity}>
                                                <input type="number" value={quantity} min="1" max="5" onChange={e => setQuantity(e.target.value)} />
                                            </td>
                                            <td className={styles.productTotal}>$52.00</td>
                                            <td className={styles.productRemove}><FaTrashAlt /></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div className={styles.btnClearCart}>
                                <button type="button" className='theme-btn-one btn-black-overlay btn_sm'>Clear cart</button>
                            </div>
                        </div>
                        <div className={styles.coupon}>
                            <form onSubmit={handleSubmit(onSubmit)}>
                                <input
                                    type="text"
                                    placeholder="Coupon code"
                                    {...register("coupon", {})}
                                />
                                <button type="button" className='theme-btn-one btn-black-overlay btn_sm'>Apply coupon</button>
                            </form>
                        </div>
                    </Col>
                    <Col lg={12} md={12}>
                        <div className={styles.cartTotal}>
                            <h3>Cart Total</h3>
                            <div className={styles.cartInner}>
                                <div className={styles.cartSubTotal}>
                                    <p>Subtotal</p>
                                    <p className={styles.cartSubTotalDetail}>$159.00</p>
                                </div>
                                <div className={styles.cartSubTotal}>
                                    <p>Coupon</p>
                                    <p className={styles.cartSubTotalDetail}><span>Discount: </span>- 20%</p>
                                </div>
                                <div className={`${styles.cartSubTotal} ${styles.border}`}>
                                    <p>Total</p>
                                    <p className={styles.cartSubTotalDetail}>$159.00</p>
                                </div>
                                <div className={styles.checkoutBtn}>
                                    <Link to="" className='theme-btn-one btn-black-overlay btn_sm'>Proceed to Checkout</Link>
                                </div>
                            </div>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default Cart
