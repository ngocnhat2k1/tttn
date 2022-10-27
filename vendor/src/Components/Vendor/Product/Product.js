import React, { useState, useEffect } from 'react'
import 'bootstrap/dist/css/bootstrap.min.css';
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import '../DashBoard.css'
import './Product.css'
import axios from '../../../service/axiosClient';
import Cookies from 'js-cookie';
import ReactPaginate from 'react-paginate'
import { FakeProducts } from '../FakeData/FakeProduct';
import styles from './PaginatedItems.module.scss'
import ListProducts from './ListProduct/ListProduct';
import { Link } from 'react-router-dom';


const Product = () => {
    const [listProducts, setistProducts] = useState(FakeProducts);
    const [currentProduct, setcurrentProduct] = useState([])

    const itemsPerPage = 8;

    const [pageCount, setPageCount] = useState(0);
    const [itemOffset, setItemOffset] = useState(0);
    useEffect(() => {
        const endOffset = itemOffset + itemsPerPage;
        setcurrentProduct(listProducts.slice(itemOffset, endOffset));
        setPageCount(Math.ceil(listProducts.length / itemsPerPage));
    }, [itemOffset, itemsPerPage, listProducts]);

    const handlePageClick = (event) => {
        const newOffset = event.selected * itemsPerPage % listProducts.length;
        setItemOffset(newOffset);
        window.location.pathname(`/${pageCount}`)
    };
    useEffect(() => {
        axios
            .get(`http://127.0.0.1:8000/api/v1/products`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                if (response.data.success) {
                    console.log(response.data.data)
                    setistProducts(response.data.data)

                } else {
                    alert('cccc');
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }, [])
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
                                    <Link data-toggle="tab" className="theme-btn-one bg-black btn_sm add_prod_button" to="/add-products">
                                        Add Product
                                    </Link>
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
                                            <ListProducts currentProduct={currentProduct} />

                                        </tbody>
                                    </table>
                                    < Col lg={12}>
                                        <ReactPaginate
                                            nextLabel="»"
                                            onPageChange={handlePageClick}
                                            pageRangeDisplayed={3}
                                            marginPagesDisplayed={2}
                                            pageCount={pageCount}
                                            previousLabel="«"
                                            pageClassName={styles.pageItem}
                                            pageLinkClassName={styles.pageLink}
                                            previousClassName={styles.pageItem}
                                            previousLinkClassName={styles.pageLink}
                                            nextClassName={styles.pageItem}
                                            nextLinkClassName={styles.pageLink}
                                            breakLabel="..."
                                            breakClassName={styles.pageItem}
                                            breakLinkClassName={styles.pageLink}
                                            containerClassName={styles.pagination}
                                            activeClassName={styles.active}
                                            renderOnZeroPageCount={null}
                                        />
                                    </Col>
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