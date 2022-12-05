import styles from './FacebookInfo.module.scss'
import { FaFacebookF } from "react-icons/fa"
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import BaoAvatar from '../../images/Bao_avatar.jpg'
import NhatAvatar from '../../images/Nhat_avatar.jpg'
import PhongAvatar from '../../images/Phong_avatar.jpg'

function FacebookInfo() {
    return (
        <section>
            <Container>
                <Row>
                    <Col lg={12}>
                        <div className={styles.textCenter}>
                            <h2>THEO DÕI CHÚNG TÔI</h2>
                            <p>Theo dõi để được cập nhật những thông tin mới nhất trên Facebook của chúng tôi</p>
                        </div>
                    </Col>
                </Row>
                <Row>
                    <Col lg={12}>
                        <div className={styles.parent}>
                            <div className={styles.children}>
                                <a href="https://www.facebook.com/lequocbao241/" target="_blank" >
                                    <img src={BaoAvatar} alt="Avatar Lê Quốc Bảo" />
                                    <div className={styles.iconFacebook}>
                                        <FaFacebookF className={styles.icon} />
                                    </div>
                                </a>
                            </div>
                            <div className={styles.children}>
                                <a href="https://www.facebook.com/n0ide4" target="_blank">
                                    <img src={PhongAvatar} alt="Avatar Đỗ Kim Phong" />
                                    <div className={styles.iconFacebook}>
                                        <FaFacebookF className={styles.icon} />
                                    </div>
                                </a>
                            </div>
                            <div className={styles.children}>
                                <a href="https://www.facebook.com/ngocnhat2k1" target="_blank">
                                    <img src={NhatAvatar} alt="Avatar Trần Ngọc Nhật" />
                                    <div className={styles.iconFacebook}>
                                        <FaFacebookF className={styles.icon} />
                                    </div>
                                </a>
                            </div>
                            <div className='clear'></div>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}
export default FacebookInfo