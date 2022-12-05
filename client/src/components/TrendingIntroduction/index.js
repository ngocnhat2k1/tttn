import styles from './TrendingIntroduction.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { Link } from 'react-router-dom'

function TrendingIntroduction() {
    return (
        <section id={styles.trendingIntroduction}>
            <Container>
                <Row>
                    <Col lg={{ span: 4, offset: 4 }} md={12} sm={12} xs={12}>
                        <div className={styles.trendingText}>
                            <h5>THỊNH HÀNH</h5>
                            <h2>SẢN PHẨM MỚI</h2>
                            <p>
                                Những sản phẩm mới luôn được cập nhật để phù hợp hơn với xu hướng của giới trẻ hiện nay
                            </p>
                            <Link to="/shop">Xem thêm</Link>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default TrendingIntroduction