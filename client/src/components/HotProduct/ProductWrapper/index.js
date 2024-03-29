import styles from './ProductWrapper.module.css'
import Col from 'react-bootstrap/Col';
import axios from 'axios';
import { Link } from 'react-router-dom'
import { FaExpand } from "react-icons/fa";
import { formatter } from '../../../utils/utils';
import { useEffect, useState } from 'react';
import ModalAddToWishList from './ModalAddToWishList';
import ModalAddToCart from './ModalAddToCart';
import ModalDetailProduct from '../../ModalDetailProduct';

function ProductWrapper({ unit }) {

    const [listNewArrival, setListNewArrival] = useState([]);
    // const [listTrending, setListTrending] = useState([]);
    const [listBestSellers, setListBestSellers] = useState([]);
    const [listOnSell, setListOnSell] = useState([]);
    const [list, setList] = useState([]);
    const [show, setShow] = useState(false);

    useEffect(() => {
        axios
            .get(`http://localhost:8000/api/product/newArrival`)
            .then(response => {
                setListNewArrival(response.data.data)
            })
            .catch(err => {
                console.log(err);
            })

        axios
            .get(`http://localhost:8000/api/product/saleProduct`)
            .then(response => {
                setListOnSell(response.data.data)
            })
            .catch(err => {
                console.log(err);
            })

        axios
            .get(`http://127.0.0.1:8000/api/product/bestSeller`)
            .then(response => {
                console.log("3", response.data)
                setListBestSellers(response.data)
            })
            .catch(err => {
                console.log(err);
            })

        // axios
        //     .get(`http://localhost:8000/api/product/trending/day=7`)
        //     .then(response => {
        //         console.log(response.data.data)
        //         setListTrending(response.data.data)
        //     })
        //     .catch(err => {
        //         console.log(err);
        //     })
    }, [])

    useEffect(() => {
        if (unit === "Mới nhập") {
            setList(listNewArrival);
        } else if (unit === "Giảm giá") {
            setList(listOnSell);
        } else if (unit === "Bán chạy") {
            setList(listBestSellers)
        }
        //  else if (unit === "Trending") {
        //     setList(listTrending)
        // }
    }, [unit, listBestSellers, listNewArrival, listOnSell])

    return (
        <>
            {Object.values(list).map((product) => {
                return (
                    <Col lg={3} md={4} sm={6} xs={12} key={product.id}>
                        <div className={styles.productWrapper}>
                            <div className={styles.thumb}>
                                <Link to={`/shop/${product.id}`} className={styles.image}>
                                    <img src={product.img} alt={product.name} />
                                </Link>
                                <span className={styles.badges}>
                                    <span
                                        className={
                                            unit === "Mới nhập" ? styles.new : unit === "Bán chạy" ? styles.best : unit === "Trending" ? styles.trending : styles.sale
                                        }>
                                        {unit === "Giảm giá" ? "Giảm " + product.percentSale + "%" : unit}</span>
                                </span>
                                <div className={styles.actions}>
                                    <ModalAddToWishList productId={product.id} />
                                    <ModalDetailProduct productId={product.id} />
                                </div>
                                <ModalAddToCart productId={product.id} />
                            </div>
                            <div className={styles.content}>
                                <h5 className={styles.title}>
                                    <Link to={`/shop/${product.id}`}>{product.name}</Link>
                                </h5>
                                <span className={styles.price}>
                                    {formatter.format(product.price * ((100 - product.percentSale) / 100))}
                                </span>
                            </div>
                        </div>
                    </Col>
                )
            })}
        </>
    )
}

export default ProductWrapper