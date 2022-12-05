import styles from './HotProduct.module.css'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import ProductWrapper from './ProductWrapper';
import { useState } from 'react';

function HotProduct() {
    const [unit, setUnit] = useState('Mới nhập');

    return (
        <section className="pb100">
            <Container>
                <Row>
                    <Col lg={12}>
                        <div className={styles.Content}>
                            <h2>NỔI BẬT</h2>
                            <p>Những sản phẩm nổi bật ở Hướng Dương Shop</p>
                        </div>
                    </Col>
                </Row>
                <Row>
                    <Col lg={12}>
                        <div>
                            <ul className={styles.navTabs}>
                                <li className={unit === "Mới nhập" ? styles.tabOnClick : ''} onClick={() => setUnit('Mới nhập')}>MỚI NHẬP</li>
                                {/* <li className={unit === "Trending" ? styles.tabOnClick : ''} onClick={() => setUnit('Trending')}>TRENDING</li> */}
                                <li className={unit === "Bán chạy" ? styles.tabOnClick : ''} onClick={() => setUnit('Bán chạy')}>BÁN CHẠY</li>
                                <li className={unit === "Giảm giá" ? styles.tabOnClick : ''} onClick={() => setUnit('Giảm giá')}>GIẢM GIÁ</li>
                            </ul>
                        </div>
                    </Col>
                    <Col lg={12}>
                        <div>
                            <div>
                                <div>
                                    <Row>
                                        <ProductWrapper unit={unit} />
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