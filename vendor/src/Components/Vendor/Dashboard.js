import React, { useState, useEffect } from 'react'
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import Cookies from 'js-cookie';
import axios from 'axios';
import './DashBoard.css'

const Dashboard = () => {


    const [RecentOrders, setRecenOrders] = useState([])
    const [totalProduct, setTotalProduct] = useState(0)
    useEffect(() => {
        axios
            .get(`http://127.0.0.1:8000/api/v1/orders`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`,
                },
            })
            .then((response) => {
                setRecenOrders(response.data.data)

            })
    }, [RecentOrders])
    useEffect(() => {
        axios
            .get(`http://127.0.0.1:8000/api/v1/products`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`,
                },
            })
            .then((response) => {
                setTotalProduct(response.data.meta.total)

            })
    }, [totalProduct])

    // for (let RecentOrder of RecentOrders) {

    // }
    return (
        <Col sm={12} md={12} lg={9}>
            <div className='tab-content dashboard_content'>
                <div className='tab-pane fade show active'>
                    <Row>
                        <Col lg={4} md={4} sm={6} xs={12}>
                            <div className='vendor_top_box'>
                                <h2>{totalProduct}</h2>
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
                                    {RecentOrders && RecentOrders.map((Order) => {
                                        return (
                                            <tr key={Order.id}>
                                                <td>
                                                    <a className='text-primary' href=".">{Order.id}</a>
                                                </td>
                                                <td>{Order.nameReceiver}</td>
                                                <td>
                                                    {Order.deletedBy ? <span className='Cancelled'>Cancelled</span> : Order.status === 0 ? <span className='Pending'>Pending</span> : Order.status === 1 ? <span className='Confirmed'>Confirm</span> : <span className='Completed'>Completed</span>}
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

export default Dashboard;