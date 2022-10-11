import React from 'react'
import { FaEdit, FaTrash } from 'react-icons/fa'
import 'bootstrap/dist/css/bootstrap.min.css';
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import '../DashBoard.css'
import './Product.css'
import { FakeProducts } from '../FakeData/FakeProduct';


const Product = () => {
    return (
        <Col sm={12} md={12} lg={9}>
            <div className='tab-content dashboard_content'>
                <div className='tab-pane fade show active'>
                    <Row>
                        <Col lg={12} md={12} sm={12} xs={12} className='position-relative'>
                            <div className='vendor_order_boxed pt-4'>
                                <div className='mb-2'>
                                    <h4>
                                        All Product
                                    </h4>
                                    <a data-toggle="tab" className="theme-btn-one bg-black btn_sm add_prod_button" href="/vendor/add-products">
                                        Add Product
                                    </a>
                                </div>
                                <div className='table-responsive'>
                                    <table className='table pending_table'>
                                        <thead className='thead-light'>
                                            <tr>
                                                <th scope="col">Image</th>
                                                <th scope="col">Product Name</th>
                                                <th scope="col">Category</th>
                                                <th scope="col">Price</th>
                                                <th scope="col">Stock</th>
                                                <th scope="col">Sales</th>
                                                <th scope="col">Edit/Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {FakeProducts.map((FakeProduct) => {
                                                return (
                                                    <tr>
                                                        <td><a><img width="70px" src={FakeProduct.Image} alt="img" /></a></td>
                                                        <td><a href="/product-details-one/1">{FakeProduct.ProductName}</a></td>
                                                        <td>{FakeProduct.Category}</td>
                                                        <td>${FakeProduct.Price}</td>
                                                        <td>{FakeProduct.Stock}</td>
                                                        <td>{FakeProduct.Sales}</td>
                                                        <td><a href="/vendor/add-products">
                                                            <FaEdit></FaEdit>
                                                        </a>
                                                            <button type="">
                                                                <FaTrash></FaTrash>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                )
                                            })}
                                        </tbody>
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

export default Product