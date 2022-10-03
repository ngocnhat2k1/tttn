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
                            <h5>TRENDING</h5>
                            <h2>New Fashion</h2>
                            <p>
                                Consectetur adipisicing elit. Dolores nisi distinctio magni, iure deserunt doloribus optio
                            </p>
                            <Link to="">Shop Now</Link>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default TrendingIntroduction