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
                                <h2>Your order is completed!</h2>
                            </div>
                            <p>Thank you for your order! Your order is being processed and will be completed within 3-6 hours. You will receive an email confirmation when your order is completed.</p>
                            <Link to="/shop" className='theme-btn-one bg-black btn_sm'>Continue Shopping</Link>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default OrderCompleteArea