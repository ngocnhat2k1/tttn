import styles from './ProductIntroduction.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import BaloTibi from '../../images/balo_tibi.png'
import BaloBeTrai from '../../images/balo_betrai.png'
import BaloNam from '../../images/balo_nam.png'
import BaloLaptop from '../../images/balo_laptop.png'
import BaloTibiCenter from '../../images/balo_tibi_mau_center.png'

function ProductIntroduction() {
    return (
        <section>
            <Container fluid>
                <Row>
                    <Col lg={4} md={6}>
                        <div className={styles.imageProduct}>
                            <img src={BaloTibi} alt="Balo TiBi Mẫu" />
                            <div className={styles.textProduct}>
                                <h4 className='colorOrange'>OUTERWEAR</h4>
                                <h2>NEW</h2>
                                <h4>COLLECTION</h4>
                                <a href="" className='theme-btn-one btn_sm bg-black'>SHOP NOW</a>
                            </div>
                        </div>
                        <div className={styles.imageProduct}>
                            <img src={BaloBeTrai} alt="Balo Bé trai Mẫu" />
                            <div className={styles.textProduct}>
                                <h4 className='colorOrange'>SUMMER</h4>
                                <h2>HOT</h2>
                                <h4>COLLECTION</h4>
                                <a href="" className='theme-btn-one btn_sm bg-black'>SHOP NOW</a>
                            </div>
                        </div>
                    </Col>
                    <Col lg={4} md={6}>
                        <div className={styles.imageProductCenter}>
                            <img src={BaloTibiCenter} alt="Balo TiBi Xanh Mẫu" />
                            <div className={styles.textProductCenter}>
                                <h2 className='colorOrange'>40%  OFFER</h2>
                                <h4>NO SELECTED MODELS</h4>
                                <a href="" className='theme-btn-one btn_sm bg-black'>SHOP NOW</a>
                            </div>
                        </div>
                    </Col>
                    <Col lg={4} md={6} className={styles.imageProductLastChild}>
                        <div className={styles.imageProduct}>
                            <img src={BaloNam} alt="Balo Nam Mẫu" />
                            <div className={styles.textProduct}>
                                <h2>NEW</h2>
                                <h4 className='colorOrange'>ARRIVALS</h4>
                                <a href="" className='theme-btn-one btn_sm bg-black'>SHOP NOW</a>
                            </div>
                        </div>
                        <div className={styles.imageProduct}>
                            <img src={BaloLaptop} alt="Balo Laptop Mẫu" />
                            <div className={styles.textProduct}>
                                <h2>HOT</h2>
                                <h4 className='colorOrange'>OFFER</h4>
                                <a href="" className='theme-btn-one btn_sm bg-black'>SHOP NOW</a>
                            </div>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default ProductIntroduction