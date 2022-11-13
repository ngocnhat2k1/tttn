import React from 'react'
import { Link, useSearchParams } from "react-router-dom";
import 'bootstrap/dist/css/bootstrap.min.css';
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import '../DashBoard.css'
import usePaginate from "../../Hook/usePagination/usePaginate";
import ListOrder from './ListOrder/ListOrder';
import styles from '../../Hook/usePagination/PaginatedItems.module.scss'


const Order = () => {
    const [searchParams] = useSearchParams();
    const { data, page, nextPage, prevPage, lastPage } = usePaginate(
        "http://127.0.0.1:8000/api/v1/orders",
        searchParams
    );

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
                                        <thead>
                                            <tr>
                                                <th scope='col'>Order Id</th>
                                                <th scope='col'>Product Detail</th>
                                                <th scope='col'>Status</th>
                                                <th scope='col'>Price</th>
                                                <th scope='col'>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <ListOrder currentOrder={data} />

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

export default Order