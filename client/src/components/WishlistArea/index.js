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

    useEffect(() => {
        axios
            .get(`http://localhost:8000/api/user/favorite`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                setListWishlist(response.data.data);
            })
            .catch(function (error) {
                console.log(error);
            });
    }, []);

    return (
        <>
            {listWishlist.length === 0 && <EmptyWishlist />}
            {listWishlist.length !== 0 &&
                <section id={styles.wishlistArea} className='ptb100'>
                    <Container>
                        <Row>
                            <Col xs={12}>
                                <div className={styles.tableDesc}>
                                    <div className={`${styles.tablePage} ${styles.tableResponsive}`}>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th className={styles.productThumb}>Image</th>
                                                    <th className={styles.productName}>Product</th>
                                                    <th className={styles.productPrice}>Price</th>
                                                    <th className={styles.productStock}>Stock Status</th>
                                                    <th className={styles.productAddCart}>Add to cart</th>
                                                    <th className={styles.productRemove}>Remove</th>
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