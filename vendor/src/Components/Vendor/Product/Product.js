import React from 'react'
import { Link, useSearchParams } from 'react-router-dom';
import 'bootstrap/dist/css/bootstrap.min.css';
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import '../DashBoard.css'
import './Product.css'
import usePaginate from "../../Hook/usePaginate";
import styles from './PaginatedItems.module.scss'
import ListProducts from './ListProduct/ListProduct';



const Product = () => {
    const [searchParams] = useSearchParams();
    const { data, page, nextPage, prevPage, lastPage } = usePaginate(
        "http://localhost:8000/api/v1/products",
        searchParams
    );

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
                                                <th scope="col">Sales</th>
                                                <th scope="col">Edit/Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <ListProducts listProducts={data} />
                                        </tbody>
                                    </table>
                                    < Col lg={12}>
                                        <ul className={styles.pagination}>
                                            {page > 1 && <li className={styles.pageItem}>
                                                <Link to={`?page=${prevPage}`} className={styles.pageLink}>«</Link>
                                            </li>}
                                            {page === lastPage && <li className={styles.pageItem}>
                                                <Link to={`?page=${1}`} className={styles.pageLink}>1</Link>
                                            </li>}
                                            {page === lastPage && <li className={`${styles.pageItem} ${styles.disable}`}>
                                                <Link className={styles.pageLink}>...</Link>
                                            </li>}
                                            {page - 1 > 0 && <li className={styles.pageItem}><Link to={`?page=${prevPage}`} className={styles.pageLink}>{page - 1}</Link></li>}

                                            <li className={`${styles.pageItem} ${styles.active}`}>
                                                <Link to={`?page=${page}`} className={styles.pageLink}>{page}</Link>
                                            </li>
                                            {page !== lastPage && <li className={styles.pageItem}>
                                                <Link to={`?page=${nextPage}`} className={styles.pageLink}>{page + 1}</Link>
                                            </li>}
                                            {page - 1 === 0 && <li className={styles.pageItem}><Link to={`?page=${page + 2}`} className={styles.pageLink}>{page + 2}</Link></li>}
                                            {page !== lastPage && <li className={`${styles.pageItem} ${styles.disable}`}>
                                                <Link className={styles.pageLink}>...</Link>
                                            </li>}
                                            {page !== lastPage && <li className={styles.pageItem}>
                                                <Link to={`?page=${lastPage}`} className={styles.pageLink}>{lastPage}</Link>
                                            </li>}
                                            {page !== lastPage && <li className={styles.pageItem}>
                                                <Link to={`?page=${nextPage}`} className={styles.pageLink}>»</Link>
                                            </li>}
                                        </ul>
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