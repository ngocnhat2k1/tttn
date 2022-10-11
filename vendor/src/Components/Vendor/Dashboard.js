import React from 'react'
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import './DashBoard.css'
import { Orders } from './FakeData/FakeData';

const Dashboard = () => {
    return (
        <Col sm={12} md={12} lg={9}>
            <div className='tab-content dashboard_content'>
                <div className='tab-pane fade show active'>
                    <Row>
                        <Col lg={4} md={4} sm={6} xs={12}>
                            <div className='vendor_top_box'>
                                <h2>25</h2>
                                <h4>Total Product</h4>
                            </div>
                        </Col>
                        <Col lg={4} md={4} sm={6} xs={12}>
                            <div className='vendor_top_box'>
                                <h2>2552</h2>
                                <h4>Total Sales</h4>
                            </div>
                        </Col>
                        <Col lg={4} md={4} sm={6} xs={12}>
                            <div className='vendor_top_box'>
                                <h2>50</h2>
                                <h4>Order Pending</h4>
                            </div>
                        </Col>
                    </Row>
                    <Row>
                        <Col lg={12} md={12} sm={12} xs={12}>
                            <div className='vendor_order_boxed pt-4'>
                                <h4>
                                    Recent Order
                                </h4>
                            </div>
                            <table className='table pending_table'>
                                <thead className='thead-light'>
                                    <tr>
                                        <th scope='col'>Order ID</th>
                                        <th scope='col'>Order Details</th>
                                        <th scope='col'>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {Orders.map((Order) => {
                                        return (
                                            <tr>
                                                <td>
                                                    <a className='text-primary' href=".">{Order.OrderId}</a>
                                                </td>
                                                <td>{Order.Details}</td>
                                                <td>
                                                    <span className={Order.Status}>{Order.Status}</span>
                                                </td>
                                            </tr>
                                        )
                                    })}
                                </tbody>
                            </table>
                        </Col>
                    </Row>
                </div>
            </div>
        </Col >
    )
}

export default Dashboard