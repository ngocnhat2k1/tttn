import styles from './Cart.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { Link } from 'react-router-dom';
import { useState, useEffect } from 'react';
import { useForm } from "react-hook-form";
import EmptyCart from './EmptyCart';
import axios from "axios";
import Cookies from 'js-cookie'
import ListProduct from './ListProduct';

function CartArea() {
    const [listProduct, setListProduct] = useState([]);

    useEffect(() => {
        axios
            .get(`http://localhost:8000/api/user/cart`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                setListProduct(response.data.data.products);
            })
            .catch(function (error) {
                console.log(error);
            });
    }, []);

    const {
        register,
        handleSubmit,
        formState: { errors },
    } = useForm();

    const onSubmit = (data) => {
        console.log(data);
    }

    return (
        <>
            {listProduct.length === 0 && <EmptyCart />}
            {listProduct.length !== 0 &&
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
                                                <ListProduct list={listProduct}/>
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
                                        <button type="submit" className='theme-btn-one btn-black-overlay btn_sm'>Apply coupon</button>
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
            }
        </>
    )
}

export default CartArea
