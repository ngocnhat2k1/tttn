import React from 'react'
import { Routes, Route } from 'react-router-dom'
import "./VendorArea.css"
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import TabList from './TabList';
import DashBoard from './Dashboard.js'
import Product from './Product/Product'
import Order from './Order/Order'
import Users from './Users/Users'
import AddProduct from './AddProduct/AddProduct'
import Category from './Category/Category';
import Voucher from './Voucher/Voucher';


const VendorArea = () => {
    return (
        <section id='vendor_area' className='ptb-100'>
            <Container>
                <Row>
                    <Col sm={12} md={12} lg={3}>
                        <TabList />
                    </Col>
                    <Routes>
                        <Route path='*' element={<DashBoard />} />
                        <Route path='/all-product' element={<Product />} />
                        <Route path='/all-order' element={<Order />} />
                        <Route path='/vendor-users' element={<Users />} />
                        <Route path='/add-products' element={<AddProduct />} />
                        <Route path='/vendor-category' element={<Category />} />
                        <Route path='/vendor-voucher' element={<Voucher />} />
                    </Routes>
                </Row>
            </Container>

        </section>
    )
}

export default VendorArea