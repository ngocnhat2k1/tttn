import styles from './Footer.module.scss'
import { FaFacebookF, FaTwitter, FaInstagram, FaLinkedinIn, FaGoogle } from "react-icons/fa"
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import Logo from '../../images/Logo.png';
import { useForm } from "react-hook-form";

function Footer() {

    const {
        register,
        handleSubmit,
        formState: { errors }
    } = useForm();

    const onSubmit = (data) => {
        console.log({ data })
    };

    return (
        <footer>
            <Container>
                <Row>
                    <Col lg={4} md={12} sm={12} xs={12}>
                        <div className={styles.footerLeft}>
                            <a href=".">
                                <img src={Logo} alt="logo" width={200} />
                            </a>
                            <p><strong>Hướng Dương Shop</strong> là cửa hàng chuyên mua bán và cung cấp các mặt hàng về balo, cặp sách với nhiều mẫu mã và lựa chọn khác nhau .</p>
                            <div className={styles.divFooterIcon}>
                                <ul>
                                    <li>
                                        <FaFacebookF fontSize={18} />
                                    </li>
                                    <li>
                                        <FaTwitter fontSize={18} />
                                    </li>
                                    <li>
                                        <FaInstagram fontSize={18} />
                                    </li>
                                    <li>
                                        <FaLinkedinIn fontSize={18} />
                                    </li>
                                    <li>
                                        <FaGoogle fontSize={18} />
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </Col>
                    <Col lg={2} md={6} sm={12} xs={12}>
                        <div className={styles.footerRow}>
                            <h3>THÔNG TIN</h3>
                            <ul>
                                <li>Trang Chủ </li>
                                <li>Về chúng tôi</li>
                                <li>Điều khoản, chính sách</li>
                                <li>Câu hỏi thường gặp</li>
                            </ul>
                        </div>
                    </Col>
                    <Col lg={3} md={6} sm={12} xs={12}>
                        <div className={styles.footerRow}>
                            <h3>CỬA HÀNG</h3>
                            <ul>
                                <li>TP HCM</li>
                                <li>0395115641</li>
                                <li>Comming soon</li>
                                <li>Huongduongshop@gmail.com</li>
                            </ul>
                        </div>
                    </Col>
                    <Col lg={3} md={12} sm={12} xs={12}>
                        <div className={styles.footerRow}>
                            <h3>PHẢN HỒI</h3>
                            <div className={styles.divForm}>
                                <form>
                                    <div>
                                        <input className={styles.inputForm} type='email' placeholder='Nhập mail của bạn' name='EMAIL' />
                                    </div>
                                    <div>
                                        <button className={styles.btnSendMail} type='submit' name='subscribe'>GỬI MAIL</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </Col>
                </Row>
            </Container>
        </footer>
    )
}

export default Footer