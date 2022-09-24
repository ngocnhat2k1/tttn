import styles from './HotProduct.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import {Tabs, Tab} from 'react-bootstrap'

function HotProduct() {
    return (
        <section className="pb50">
            <Container>
                <Row>
                    <Col lg={12}>
                        <div className={styles.Content}>
                            <h2>HOT PRODUCTS</h2>
                            <p>See What Everyone Is Shopping from Andshop E-Comerce</p>
                        </div>
                    </Col>
                </Row>
                <Row>
                    <Col lg={12}>
                        <div>
                            <ul className={styles.navTabs}>
                                <li >NEW ARRIVAL</li>
                                <li>TRENDING</li>
                                <li>BEST SELLERS</li>
                                <li>FEATURED</li>
                                <li>ON SALL</li>
                            </ul>
                        </div>
                    </Col>
                    <Col lg={12}>
                        <div>
                            <div>
                                <div>
                                    <Row>
                                        <Col lg={3} md={4} sm={6} xs={12}>

                                        </Col>
                                        <Col lg={3} md={4} sm={6} xs={12}>
                                            
                                        </Col>
                                        <Col lg={3} md={4} sm={6} xs={12}>
                                            
                                        </Col>
                                        <Col lg={3} md={4} sm={6} xs={12}>
                                            
                                        </Col>
                                        <Col lg={3} md={4} sm={6} xs={12}>
                                            
                                        </Col>
                                        <Col lg={3} md={4} sm={6} xs={12}>
                                            
                                        </Col>
                                        <Col lg={3} md={4} sm={6} xs={12}>
                                            
                                        </Col>
                                        <Col lg={3} md={4} sm={6} xs={12}>
                                            
                                        </Col>
                                    </Row>
                                </div>
                            </div>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default HotProduct