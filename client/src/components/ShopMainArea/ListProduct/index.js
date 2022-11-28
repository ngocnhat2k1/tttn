import styles from './ListProduct.module.scss'
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import { FaRegHeart, FaExpand } from "react-icons/fa";
import { formatter } from '../../../utils/utils'

function ListProduct({ currentItems }) {
    return (
        <Row>
            {currentItems && Object.values(currentItems).map((product) => {
                return (
                    <Col lg={4} md={4} sm={6} xs={12} key={product.id}>
                        <div className={styles.productWrapper}>
                            <div className={styles.thumb}>
                                <a href="" className={styles.image}>
                                    <img src={product.image} alt={product.name} />
                                </a>
                                <span className={styles.badges}>
                                    <span
                                        className={
                                            product.percentSale !== "" ? styles.sale : ""
                                        }>
                                        {product.percentSale !== "" ? product.percentSale + "% OFF" : product.productUnit}</span>
                                </span>
                                <div className={styles.actions}>
                                    <a href="" className={`${styles.wishList} ${styles.action}`} title="Wishlist">
                                        <FaRegHeart />
                                    </a>
                                    <a href="" className={`${styles.quickView} ${styles.action}`} title="Quickview">
                                        <FaExpand />
                                    </a>
                                </div>
                                <button className={`${styles.addToCart}`}>Add to cart</button>
                            </div>
                            <div className={styles.content}>
                                <h5 className={styles.title}>
                                    <a href="">{product.name}</a>
                                </h5>
                                <span className={styles.price}>
                                    {product.percentSale !== "" ? formatter.format(product.price * ((100 - product.percentSale) / 100)) : formatter.format(product.price)}
                                </span>
                            </div>
                        </div>
                    </Col>
                )
            })
            }
        </Row>
    )
}

export default ListProduct