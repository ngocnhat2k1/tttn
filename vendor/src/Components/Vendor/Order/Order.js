import React from 'react'
import 'bootstrap/dist/css/bootstrap.min.css';
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import '../DashBoard.css'
import './Order.css'

const Oder = () => {
    return (
        <Col sm={12} md={12} lg={9}>
            <div className='tab-content dashboard_content'>
                <div className='tab-pane fade show active'>
                    <Row>
                        <Col lg={12} md={12} sm={12} xs={12}>
                            <div className='vendor_order_boxed'>
                                <h4>All order</h4>
                                <div className='table-resposive'>
                                    <table className='table pending_table'>
                                        <thead className='thead-light'>
                                            <tr>
                                                <th></th>
                                            </tr>
                                        </thead>
                                    </table>

                                </div>

                            </div>
                        </Col>
                    </Row>
                </div>

            </div>
        </Col>
    )
}

export default Oder