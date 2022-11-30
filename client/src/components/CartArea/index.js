import styles from './Cart.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { Link } from 'react-router-dom';
import { useState, useEffect } from 'react';
import EmptyCart from './EmptyCart';
import axios from "axios";
import Cookies from 'js-cookie'
import ListProduct from './ListProduct';

function CartArea() {
    const [listProduct, setListProduct] = useState([]);

    useEffect(() => {
        axios
            .get(`http://localhost:8000/api/user/cart/state=all`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then(response => {
                setListProduct(response.data.data);
            })
            .catch(error => {
                console.log(error);
            });
    }, []);

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
                                                    <th className={styles.productThumb}>Hình ảnh</th>
                                                    <th className={styles.productName}>Sản phẩm</th>
                                                    <th className={styles.productPrice}>Giá tiền</th>
                                                    <th className={styles.productQuantity}>Số lượng</th>
                                                    <th className={styles.productTotal}>Tổng tiền</th>
                                                    <th className={styles.productRemove}>Bỏ khỏi giỏ hàng</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <ListProduct list={listProduct}/>
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
