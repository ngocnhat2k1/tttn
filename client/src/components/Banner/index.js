import styles from './Banner.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import ModelBanner from '../../images/modelBanner.png'
import {Link} from 'react-router-dom'

function Banner() {
    return (
        <section id={styles.banner}>
            <Container>
                <Row>
                    <Col lg={6} >
                        <div className={styles.bannerText}>
                            <h1>LIVE FOR <span>FASHION</span></h1>
                            <h3>SALE UP TO 50%</h3>
                            <Link to="/shop">SHOP NOW</Link>
                        </div>
                    </Col>
                    <Col lg={6} className={styles.colImageBanner}>
                        <div className={styles.divImg}>
                            <img src={ModelBanner} alt="" />
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default Banner