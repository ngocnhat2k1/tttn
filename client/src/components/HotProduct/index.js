import styles from './HotProduct.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import ProductWrapper from './ProductWrapper';
import { useState } from 'react';

function HotProduct() {
    const [unit, setUnit] = useState('New Arrival');

    return (
        <section className="pb100">
            <Container>
                <Row>
                    <Col lg={12}>
                        <div className={styles.Content}>
                            <h2>HOT PRODUCTS</h2>
                            <p>See What Everyone Is Shopping from Andshop E-Comerce</p>
                        </div>
                    </Col>
                </Row>
                <Row>
                    <Col lg={12}>
                        <div>
                            <ul className={styles.navTabs}>
                                <li className={unit === "New Arrival" ? styles.tabOnClick : ''} onClick={() => setUnit('New Arrival')}>NEW ARRIVAL</li>
                                <li className={unit === "Trending" ? styles.tabOnClick : ''} onClick={() => setUnit('Trending')}>TRENDING</li>
                                <li className={unit === "Best Seller" ? styles.tabOnClick : ''} onClick={() => setUnit('Best Seller')}>BEST SELLERS</li>
                                <li className={unit === "On Sell" ? styles.tabOnClick : ''}onClick={() => setUnit('On Sell')}>ON SELL</li>
                            </ul>
                        </div>
                    </Col>
                    <Col lg={12}>
                        <div>
                            <div>
                                <div>
                                    <Row>
                                        <ProductWrapper productUnit={unit} />
                                    </Row>
                                </div>
                            </div>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default HotProduct