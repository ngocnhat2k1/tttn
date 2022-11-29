import React, { useState, useEffect } from 'react'
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import Cookies from 'js-cookie';
import axios from 'axios';
import './DashBoard.css'

const Dashboard = () => {
    const [orderPending, setOrderPending] = useState()
    const [RecentOrders, setRecenOrders] = useState([])
    const [totalProduct, setTotalProduct] = useState(0)
    const [totalSales, setTotalSales] = useState()
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
    }, [])

    useEffect(() => {
        axios
            .get(`http://127.0.0.1:8000/api/admin/dashboard`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`,
                },
            })
            .then((response) => {
                setOrderPending(response.data.totalOrdersPending)
                setTotalProduct(response.data.totalProducts)
                setTotalSales(response.data.totalSales)
            })
    }, [totalProduct])

    return (
        <Col sm={12} md={12} lg={9}>
            <div className='tab-content dashboard_content'>
                <div className='tab-pane fade show active'>
                    <Row>
                        <Col lg={4} md={4} sm={6} xs={12}>
                            <div className='vendor_top_box'>
                                <h2>{totalProduct}</h2>
                                <h4>Tổng Sản Phẩm</h4>
                            </div>
                        </Col>
                        <Col lg={4} md={4} sm={6} xs={12}>
                            <div className='vendor_top_box'>
                                <h2>{totalSales}</h2>
                                <h4>Tổng Đơn Hàng </h4>
                            </div>
                        </Col>
                        <Col lg={4} md={4} sm={6} xs={12}>
                            <div className='vendor_top_box'>
                                <h2>{orderPending}</h2>
                                <h4>Số Đơn Hàng Đang Xử Lí</h4>
                            </div>
                        </Col>
                    </Row>
                    <Row>
                        <Col lg={12} md={12} sm={12} xs={12}>
                            <div className='vendor_order_boxed pt-4'>
                                <h4>
                                    Những Đơn Hàng Gần Đây
                                </h4>
                            </div>
                            <table className='table pending_table'>
                                <thead className='thead-light'>
                                    <tr>
                                        <th scope='col'>Mã Đơn Hàng</th>
                                        <th scope='col'>Địa Chỉ</th>
                                        <th scope='col'>Tên Người Nhận</th>
                                        <th scope='col'>Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {RecentOrders && RecentOrders.map((Order) => {
                                        return (
                                            <tr key={Order.orderId}>
                                                <td>
                                                    <a className='text-primary' href=".">{Order.orderId}</a>
                                                </td>
                                                <td>{Order.address}</td>
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