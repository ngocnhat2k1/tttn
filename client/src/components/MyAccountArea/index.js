import styles from './MyAccountArea.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { Link, Route, Routes } from 'react-router-dom'
import { FaTachometerAlt, FaCartArrowDown, FaMapMarkerAlt, FaUserAlt } from 'react-icons/fa'
import CustomerOrder from './CustomerOrder';
import CustomerDashboard from './CustomerDashboard';
import CustomerAddress from './CustomerAddress';
import CustomerAccountDetails from './CustomerAccountDetails';
import { useState } from 'react';
import NotFound from '../NotFound';

function MyAccountArea() {
    const [tab, setTab] = useState("Dashboard");

    return (
        <section id='myAccountArea' className='ptb100 prl30'>
            <Container fluid>
                <Row>
                    <Col sm={12} md={12} lg={3}>
                        <div className={styles.tabButton}>
                            <ul role="tablist" className={`flex-column nav`}>
                                <li>
                                    <Link to="/my-account" className={tab === "Dashboard" ? styles.active : ""} onClick={() => setTab("Dashboard")}>
                                        <FaTachometerAlt /> THỐNG KÊ
                                    </Link>
                                    <Link to="/my-account/customer-order" className={tab === "Orders" ? styles.active : ""}  onClick={() => setTab("Orders")}>
                                        <FaCartArrowDown className=''/> ĐƠN HÀNG
                                    </Link>
                                    <Link to="/my-account/customer-address" className={tab === "Addresses" ? styles.active : ""} onClick={() => setTab("Addresses")}>
                                        <FaMapMarkerAlt className=''/> ĐỊA CHỈ GIAO HÀNG
                                    </Link>
                                    <Link to="/my-account/customer-account-details" className={tab === "AccountDetails" ? styles.active : ""} onClick={() => setTab("AccountDetails")}>
                                        <FaUserAlt className=''/> CHI TIẾT TÀI KHOẢN
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </Col>
                    <Col sm={12} md={12} lg={9}>
                        <Routes>
                            <Route path="/" element={<CustomerDashboard />}></Route>
                            <Route path="/customer-order" element={<CustomerOrder />}></Route>
                            <Route path="/customer-address" element={<CustomerAddress />}></Route>
                            <Route path="/customer-account-details" element={<CustomerAccountDetails />}></Route>
                            <Route path="/*" element={<NotFound />}></Route>
                        </Routes>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default MyAccountArea