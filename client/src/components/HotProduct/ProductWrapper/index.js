import styles from './ProductWrapper.module.scss'
import Col from 'react-bootstrap/Col';
import { FaRegHeart, FaExpand } from "react-icons/fa";
import { products } from './products';
import { formatter } from '../../../utils/utils';

function ProductWrapper(prop) {
    return (
        <>
            {products.map((product) => {
                if (product.productUnit === prop.productUnit) {
                    return (
                        <Col lg={3} md={4} sm={6} xs={12} key={product.id}>
                            <div className={styles.productWrapper}>
                                <div className={styles.thumb}>
                                    <a href="" className={styles.image}>
                                        <img src={product.image} alt={product.name} />
                                        {/* <img src="" alt="" /> */}
                                    </a>
                                    <span className={styles.badges}>
                                        <span
                                            className={
                                                product.productUnit === "New Arrival" ? styles.new : product.productUnit === "Best Seller" ? styles.best : product.productUnit === "Trending" ? styles.trending : styles.sale
                                            }>
                                            {product.productUnit === "On Sell" ? product.discount + "% OFF" : product.productUnit}</span>
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
                                        {product.productUnit === "On Sell" ? formatter.format(product.cost * ((100 - product.discount) / 100)): formatter.format(product.cost)}
                                        </span>
                                </div>
                            </div>
                        </Col>
                    )
                }
            })}
        </>
    )
}

export default ProductWrapper