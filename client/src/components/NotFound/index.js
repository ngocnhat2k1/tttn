import styles from './NotFound.module.css'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { Link } from 'react-router-dom';

function NotFound() {
    return(
        <section id={styles.errorArea} className='ptb100'>
            <Container>
                <Row>
                    <Col lg={{ span: 6, offset: 3 }}>
                        <div className={styles.errorWrapper}>
                            <h1>404</h1>
                            <h3>We are sorry, the page you've requested is not available</h3>
                            <Link to="/" className='theme-btn-one btn-black-overlay btn_md'>BACK TO HOME PAGE</Link>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default NotFound;