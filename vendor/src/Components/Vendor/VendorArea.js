import React from 'react'
import { Routes, Route } from 'react-router-dom'

import "./VendorArea.css"
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import TabList from './TabList';
import DashBoard from './Dashboard.js'
import Product from './Product/Product'
import Oder from './Oder/Oder'
import Profile from './Profile/Profile'
import AddProduct from './AddProduct/AddProduct'
import Setting from './Setting/Setting'


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
                        <Route path='/all-order' element={<Oder />} />
                        <Route path='/vendor-profile' element={<Profile />} />
                        <Route path='/add-products' element={<AddProduct />} />
                        <Route path='/vendor-setting' element={<Setting />} />

                    </Routes>
                </Row>
            </Container>

        </section>
    )
}

export default VendorArea