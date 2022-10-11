import styles from './TopHeader.module.scss'
import { FaUser, FaLock } from "react-icons/fa"
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { Link } from 'react-router-dom';

function TopHeader() {
    return (
        <section className={styles.divTopheader}>
            <Container className={styles.container}>
                <Row>
                    <Col lg={6} md={6} sm={12} style={{paddingLeft: 0}}>
                        <div className={styles.divLeft}>
                            <p>Special collection already available. <span className='colorOrange'>Read more...</span></p>
                        </div>
                    </Col>
                    <Col lg={6} md={6} sm={12}>
                        <div className={styles.divRight}>
                            <ul>
                                <li className={styles.liRight}>
                                    <Link to="/login"><FaUser fontSize={12} /> Login</Link>
                                </li>
                                <li className={styles.liRight}>
                                    <Link to="/register"><FaLock fontSize={12} /> Register</Link>
                                </li>
                                <div className={styles.clear}></div>
                            </ul>
                        </div>
                    </Col>
                    <div className='clear'></div>
                </Row>
            </Container>
        </section>
    )
}

export default TopHeader