import styles from '../MyAccountArea.module.scss';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';

function CustomerAddress() {
    return (
                <Row>
                    <Col lg={6}>
                        <div className={styles.myaccountContent}>
                            <h4 className={styles.title}>Shipping Address #1</h4>
                            <div className={styles.shippingAddress}>
                                <h5>
                                    <strong>Alex Porty</strong>
                                </h5>
                                <p>
                                    4964 Dennison Street French Camp, 12345
                                </p>
                                <p>Mobile: 0969710601</p>
                                <button type="button" className='theme-btn-one bg-black btn_sm mt-4'>Edit Address</button>
                            </div>
                        </div>
                    </Col>
                </Row>
    )
}

export default CustomerAddress