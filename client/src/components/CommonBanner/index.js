import styles from './CommonBanner.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';

function CommonBanner() {
    return (
        <section id={styles.commonBanner}>
            <Container>
                <Row>
                    <Col lg={12}>
                        <div className={styles.commonBannerText}>
                            <h2>Contact</h2>
                            <ul>
                                <li>
                                    <a href=".">Home</a>
                                </li>
                                <li className={styles.slash}>/</li>
                                <li className={styles.active}>Contact</li>
                            </ul>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default CommonBanner