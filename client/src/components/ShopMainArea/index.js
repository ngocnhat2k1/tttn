import styles from './ShopMainArea.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { GoSearch } from "react-icons/go";
import { useState } from 'react'
import { products } from '../HotProduct/ProductWrapper/products';
import { formatter } from '../../utils/utils'

function ShopMainArea() {

    const [category, setCategory] = useState('ALL');
    const [price, setPrice] = useState(100000);
    const [gender, setGender] = useState('ALL');

    const handlePriceFilter = (e) => {
        setPrice(e.target.value);
    }

    const handleClear = () => {
        setPrice(100000);
        setGender('ALL');
        setCategory('ALL');
    }

    return (
        <section id={styles.shopMainArea}>
            <Container fluid>
                <Row>
                    <Col lg={3}>
                        <div className={styles.shopSidebarWrapper}>
                            <div className={styles.shopSearch}>
                                <form>
                                    <input className="form-control" placeholder="Search..."></input>
                                    <button type="">
                                        <GoSearch />
                                    </button>
                                </form>
                            </div>
                            <div className={styles.shopSidebarBoxed}>
                                <h4>Product Categories</h4>
                                <form>
                                    <label className={styles.boxed}>ALL
                                        <input type="radio" name="radio"
                                            checked={category === "ALL" ? true : false}
                                            onChange={() => setCategory("ALL")}
                                        />
                                        <span className={styles.checkmark}></span>
                                    </label>
                                    <label className={styles.boxed}>Fashion
                                        <input type="radio" name="radio" checked={category === "Fashion" ? true : false}
                                            onChange={() => setCategory("Fashion")} />
                                        <span className={styles.checkmark}></span>
                                    </label>
                                    <label className={styles.boxed}>Bags
                                        <input type="radio" name="radio" checked={category === "Bags" ? true : false}
                                            onChange={() => setCategory("Bags")} />
                                        <span className={styles.checkmark}></span>
                                    </label>
                                    <label className={styles.boxed}>Jackets
                                        <input type="radio" name="radio" checked={category === "Jackets" ? true : false}
                                            onChange={() => setCategory("Jackets")} />
                                        <span className={styles.checkmark}></span>
                                    </label>
                                </form>
                            </div>
                            <div className={styles.shopSidebarBoxed}>
                                <h4>Gender</h4>
                                <form>
                                    <label className={styles.boxed}>ALL
                                        <input type="radio" name="radio"
                                            checked={gender === "ALL" ? true : false}
                                            onChange={() => setGender("ALL")}
                                        />
                                        <span className={styles.checkmark}></span>
                                    </label>
                                    <label className={styles.boxed}>Boy
                                        <input type="radio" name="radio" checked={gender === "Boy" ? true : false}
                                            onChange={() => setGender("Boy")} />
                                        <span className={styles.checkmark}></span>
                                    </label>
                                    <label className={styles.boxed}>Girl
                                        <input type="radio" name="radio" checked={gender === "Girl" ? true : false}
                                            onChange={() => setGender("Girl")} />
                                        <span className={styles.checkmark}></span>
                                    </label>
                                </form>
                            </div>
                            <div className={styles.shopSidebarBoxed}>
                                <h4>Price</h4>
                                <div className={styles.priceFilter}>
                                    <input id={styles.formControlRange} type="range" onInput={handlePriceFilter} min="100000" max="500000" />
                                    <div className={styles.price}>
                                        <span>Price: {formatter.format(price)}</span>
                                    </div>
                                </div>
                            </div>
                            <div className={styles.clearButton}>
                                <button type="button" onClick={handleClear}>CLEAR FILTER</button>
                            </div>
                        </div>
                    </Col>
                    <Col lg={9}>
                        <Row></Row>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default ShopMainArea