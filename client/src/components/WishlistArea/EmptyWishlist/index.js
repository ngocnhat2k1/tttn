import styles from './EmptyWishlist.module.css'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import EmptyCartImage from '../../../images/emptyCart.png'

function EmptyWishlist() {
    return (
        <section className='ptb100'>
            <Container>
                <Row>
                    <Col lg={{ span: 6, offset: 3 }} md={{ span: 6, offset: 3 }} sm={12} xs={12}>
                        <div className={styles.emptyCartArea}>
                            <img src={EmptyCartImage} alt="Hình danh sách yêu thích rỗng" />
                            <h2>YOUR WISHLIST IS EMPTY</h2>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default EmptyWishlist