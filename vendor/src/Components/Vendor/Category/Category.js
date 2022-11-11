import React from 'react'
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import { Link, useSearchParams } from "react-router-dom";
import 'bootstrap/dist/css/bootstrap.min.css';
import ListCategories from './ListCategories/ListCategories'
import '../DashBoard.css'
import usePaginate from "../../Hook/usePagination/usePaginate";
import styles from '../../Hook/usePagination/PaginatedItems.module.scss'

const Category = () => {
    const [searchParams] = useSearchParams();
    const { data, page, nextPage, prevPage, lastPage } = usePaginate(
        "http://127.0.0.1:8000/api/v1/categories",
        searchParams
    );

    return (
        <Col sm={12} md={12} lg={9}>
            <div className='tab-content dashboard_content'>
                <div className='tab-pane fade show active'>
                    <Row>
                        <Col lg={12} md={12} sm={12} xs={12} className='position-relative'>
                            <div className='vendors_profiles pt-4'>
                                <div className='mb-2'>
                                    <h4>
                                        All Categories
                                    </h4>
                                    <Link data-toggle="tab" className="theme-btn-one bg-black btn_sm add_prod_button" to="/add-category">
                                        Add Category
                                    </Link>
                                </div>
                                <div className='table-responsive'>
                                    <table className='table pending_table'>
                                        <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Name</th>
                                                {/* <th scope="col">Full Name</th>
                                        <th scope="col">Email</th> */}
                                                <th scope="col">Edit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <ListCategories currentCategory={data} />
                                        </tbody>
                                    </table>
                                    {/* {page !== 1 ?
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
                                        </Col> : <div>
                                        </div>} */}


                                </div>
                            </div>
                        </Col>
                    </Row>
                </div>

            </div>
        </Col>
    )
}

export default Category