import styles from './OrderComplete.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { Link } from 'react-router-dom';
import { FaCheckCircle } from 'react-icons/fa';

function OrderCompleteArea() {
    return (
        <section className='ptb100'>
            <Container>
                <Row className='justifyContentCenter'>
                    <Col md={8}>
                        <div className={styles.orderComplete}>
                            <FaCheckCircle />
                            <div className={styles.orderCompleteHeading}>
                                <h2>Bạn đã đặt hàng thành công!</h2>
                            </div>
                            <p>Cảm ơn bạn đã đặt hàng của bạn! Đơn đặt hàng của bạn đang được xử lý và sẽ được hoàn thành trong vòng 3-6 giờ. Bạn sẽ nhận được email xác nhận khi đơn đặt hàng của bạn hoàn tất.</p>
                            <Link to="/shop" className='theme-btn-one bg-black btn_sm'>Tiếp tục mua hàng</Link>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default OrderCompleteArea