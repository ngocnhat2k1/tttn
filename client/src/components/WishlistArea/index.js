import styles from './Wishlist.module.css'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import axios from "axios";
import Cookies from 'js-cookie'
import EmptyWishlist from './EmptyWishlist';
import { useState, useEffect } from 'react';
import ListProduct from './ListProduct';

function WishlistArea() {
    const [listWishlist, setListWishlist] = useState([]);
    const [check, setCheck] = useState(0);

    useEffect(() => {
        axios
            .get(`http://localhost:8000/api/user/favorite`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                setListWishlist(response.data.data);
                setCheck(check + 1);
            })
            .catch(function (error) {
                console.log(error);
            });
    }, []);

    return (
        <>
            {listWishlist.length === 0 && check > 0 && <EmptyWishlist />}
            {listWishlist.length !== 0 && check > 0 &&
                <section id={styles.wishlistArea} className='ptb100'>
                    <Container>
                        <Row>
                            <Col xs={12}>
                                <div className={styles.tableDesc}>
                                    <div className={`${styles.tablePage} ${styles.tableResponsive}`}>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th className={styles.productThumb}>Hình ảnh</th>
                                                    <th className={styles.productName}>Sản phẩm</th>
                                                    <th className={styles.productPrice}>Giá tiền</th>
                                                    <th className={styles.productStock}>Tình trạng trong kho</th>
                                                    <th className={styles.productAddCart}>Thêm vào giỏ hàng</th>
                                                    <th className={styles.productRemove}>Bỏ khỏi giỏ hàng</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <ListProduct list={listWishlist}/>
                                            </tbody>
                                        </table>
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

export default WishlistArea