import styles from './EmptyCart.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import EmptyCartImage from '../../../images/emptyCart.png'
import {Link} from 'react-router-dom'

function EmptyCart() {
    return (
        <section className='ptb100'>
            <Container>
                <Row>
                    <Col lg={{ span: 6, offset: 3 }} md={{ span: 6, offset: 3 }} sm={12} xs={12}>
                        <div className={styles.emptyCartArea}>
                            <img src={EmptyCartImage} alt="Hình giỏ hàng rỗng" />
                            <h2>Giỏ hàng không có sản phẩm nào</h2>
                            <Link className='btn_md btn-black-overlay btn' to="/shop">Tiếp tục mua hàng</Link>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default EmptyCart