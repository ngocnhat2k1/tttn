import styles from './NotFound.module.css'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { Link, useParams } from 'react-router-dom';

function NotFoundOrder() {
    const { id } = useParams();

    return (
        <section id={styles.errorArea} className='ptb100'>
            <Container>
                <Row>
                    <Col lg={{ span: 6, offset: 3 }}>
                        <div className={styles.errorWrapper}>
                            <h3>{`Không tìm thấy đơn hàng có mã ${id} của bạn`}</h3>
                            <Link to="/order-tracking" className='theme-btn-one btn-black-overlay btn_md'>TRỞ VỀ</Link>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default NotFoundOrder;