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
    const duongdan = window.location.pathname
    const [isActive, setActive] = useState(duongdan)

    return (
        <section id='myAccountArea' className='ptb100 prl30'>
            <Container fluid>
                <Row>
                    <Col sm={12} md={12} lg={3}>
                        <div className={styles.tabButton}>
                            <ul role="tablist" className={`flex-column nav`}>
                                <li>
                                    <Link to="/my-account"
                                        onClick={() => setActive('/my-account')}
                                        className={`${isActive === "/my-account" ? styles.active : ' '} `}>
                                        <FaTachometerAlt /> THỐNG KÊ
                                    </Link>
                                    <Link to="/my-account/customer-order"
                                        onClick={() => setActive('/my-account/customer-order')}
                                        className={`${isActive === "/my-account/customer-order" ? styles.active : ' '} `}>
                                        <FaCartArrowDown className='' /> ĐƠN HÀNG
                                    </Link>
                                    <Link to="/my-account/customer-address"
                                        onClick={() => setActive('/my-account/customer-address')}
                                        className={`${isActive === "/my-account/customer-address" ? styles.active : ' '} `}>
                                        <FaMapMarkerAlt className='' /> ĐỊA CHỈ GIAO HÀNG
                                    </Link>
                                    <Link to="/my-account/customer-account-details"
                                        onClick={() => setActive('/my-account/customer-account-details')}
                                        className={`${isActive === "/my-account/customer-account-details" ? styles.active : ' '} `} >
                                        <FaUserAlt className='' /> CHI TIẾT TÀI KHOẢN
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
        </section >
    )
}

export default MyAccountArea