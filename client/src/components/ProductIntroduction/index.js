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
                <Row className={styles.row}>
                    <Col lg={4} md={6} className={styles.col}>
                        <div className={styles.imageProduct}>
                            <img src={BaloTibi} alt="Balo TiBi Mẫu" />
                            <div className={styles.textProduct}>
                                <h4 className={`${styles.h4Orange} ${styles.pd5}`}>OUTERWEAR</h4>
                                <h2 className={styles.h2Content}>NEW</h2>
                                <h4 className={`${styles.h4Black} ${styles.pd20}`}>COLLECTION</h4>
                                <a href="" className={styles.btnShopNow}>SHOP NOW</a>
                            </div>
                        </div>
                        <div className={styles.imageProduct}>
                            <img src={BaloBeTrai} alt="Balo Bé trai Mẫu" />
                            <div className={styles.textProduct}>
                                <h4 className={`${styles.h4Orange} ${styles.pd5}`}>SUMMER</h4>
                                <h2 className={styles.h2Content}>HOT</h2>
                                <h4 className={`${styles.h4Black} ${styles.pd20}`}>COLLECTION</h4>
                                <a href="" className={styles.btnShopNow}>SHOP NOW</a>
                            </div>
                        </div>
                    </Col>
                    <Col lg={4} md={6} className={styles.col}>
                        <div className={styles.imageProductCenter}>
                            <img src={BaloTibiCenter} alt="Balo TiBi Xanh Mẫu" />
                            <div className={styles.textProductCenter}>
                                <h4 className={`${styles.h4Orange} ${styles.pd5} ${styles.fontSize36}`}>40%  OFFER</h4>
                                <h2 className={`${styles.h4Black} ${styles.pd5}`}>NO SELECTED</h2>
                                <h4 className={`${styles.h4Black} ${styles.pd20}`}>MODELS</h4>
                                <a href="" className={styles.btnShopNow}>SHOP NOW</a>
                            </div>
                        </div>
                    </Col>
                    <Col lg={4} md={6} className={styles.col}>
                        <div className={styles.imageProduct}>
                            <img src={BaloNam} alt="Balo Nam Mẫu" />
                            <div className={styles.textProduct}>
                                <h2 className={styles.h2Content}>NEW</h2>
                                <h4 className={`${styles.h4Orange} ${styles.pd20}`}>ARRIVALS</h4>
                                <a href="" className={styles.btnShopNow}>SHOP NOW</a>
                            </div>
                        </div>
                        <div className={styles.imageProduct}>
                            <img src={BaloLaptop} alt="Balo Laptop Mẫu" />
                            <div className={styles.textProduct}>
                                <h2 className={styles.h2Content}>HOT</h2>
                                <h4 className={`${styles.h4Orange} ${styles.pd20}`}>OFFER</h4>
                                <a href="" className={styles.btnShopNow}>SHOP NOW</a>
                            </div>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default ProductIntroduction