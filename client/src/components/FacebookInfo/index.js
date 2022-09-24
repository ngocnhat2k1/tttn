import styles from './FacebookInfo.module.scss'
import { FaFacebookF } from "react-icons/fa"
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import BaoAvatar from '../../images/Bao_avatar.jpg'
import NhatAvatar from '../../images/Nhat_avatar.jpg'

function FacebookInfo() {
    return (
        <section>
            <Container>
                <Row>
                    <Col lg={12}>
                        <div className={styles.textCenter}>
                            <h2>FOLLOW US FACEBOOK</h2>
                            <p>Follow Us and get updated from our facebook</p>
                        </div>
                    </Col>
                </Row>
                <Row>
                    <Col lg={12}>
                        <div className={styles.parent}>
                                <div className={styles.children}>
                                    <a href="">
                                        <img src={BaoAvatar} alt="Avatar Lê Bảo" />
                                        <div className={styles.iconFacebook}>
                                        <FaFacebookF className={styles.icon} />
                                        </div>
                                    </a>
                                </div>
                                <div className={styles.children}>
                                    <a href="">
                                        <img src={BaoAvatar} alt="Avatar Lê Bảo" />
                                        <div className={styles.iconFacebook}>
                                        <FaFacebookF className={styles.icon} />
                                        </div>
                                    </a>
                                </div>
                                <div className={styles.children}>
                                    <a href="">
                                        <img src={NhatAvatar} alt="Avatar Ngọc Nhật" />
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