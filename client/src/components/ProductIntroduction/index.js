import styles from './ProductIntroduction.module.scss'
import Container from 'react-bootstrap/Container';
import { Link } from 'react-router-dom'
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
                                <h4 className='colorOrange'>BALO TIBI</h4>
                                <h2>NEW</h2>
                                <h4>BỘ SƯU TẬP</h4>
                                <Link to="/shop" className='theme-btn-one btn_sm bg-black'>MUA NGAY</Link>
                            </div>
                        </div>
                        <div className={styles.imageProduct}>
                            <img src={BaloBeTrai} alt="Balo Bé trai Mẫu" />
                            <div className={styles.textProduct}>
                                <h4 className='colorOrange'>MÙA HÈ</h4>
                                <h2>HOT</h2>
                                <h4>BỘ SƯU TẬP</h4>
                                <Link to="/shop" className='theme-btn-one btn_sm bg-black'>MUA NGAY</Link>
                            </div>
                        </div>
                    </Col>
                    <Col lg={4} md={6}>
                        <div className={styles.imageProductCenter}>
                            <img src={BaloTibiCenter} alt="Balo TiBi Xanh Mẫu" />
                            <div className={styles.textProductCenter}>
                                <h2 className='colorOrange'>GIẢM ĐẾN 40%</h2>
                                <Link to="/shop" className='theme-btn-one btn_sm bg-black'>MUA NGAY</Link>
                            </div>
                        </div>
                    </Col>
                    <Col lg={4} md={6} className={styles.imageProductLastChild}>
                        <div className={styles.imageProduct}>
                            <img src={BaloNam} alt="Balo Nam Mẫu" />
                            <div className={styles.textProduct}>
                                <h2>MỚI</h2>
                                <h4 className='colorOrange'>VỀ HÀNG</h4>
                                <Link to="/shop" className='theme-btn-one btn_sm bg-black'>MUA NGAY</Link>
                            </div>
                        </div>
                        <div className={styles.imageProduct}>
                            <img src={BaloLaptop} alt="Balo Laptop Mẫu" />
                            <div className={styles.textProduct}>
                                <h2>HOT</h2>
                                <h4 className='colorOrange'>KHUYẾN MÃI</h4>
                                <Link to="/shop" className='theme-btn-one btn_sm bg-black'>MUA NGAY</Link>
                            </div>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default ProductIntroduction