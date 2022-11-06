import React from 'react'
import { Link, useSearchParams } from 'react-router-dom';
import usePaginate from "../../Hook/usePaginate";
import 'bootstrap/dist/css/bootstrap.min.css';
import Col from 'react-bootstrap/Col';
import ListUsers from './ListUsers/ListUsers';
import styles from './PaginatedItems.module.scss'
import '../DashBoard.css'
import './Users.css'

const Users = () => {
    const [searchParams] = useSearchParams();
    const { data, page, nextPage, prevPage, lastPage } = usePaginate(
        "http://127.0.0.1:8000/api/v1/users",
        searchParams
    );

    return (
        <Col sm={12} md={12} lg={9}>
            <div className='tab-content dashboard_content'>
                <div className='tab-pane fade show active'>
                    <div className='vendors_profiles'>
                        <h4>All Users</h4>

                        <div className='table-responsive'>
                            <table className='table pending_table'>
                                <thead className='thead-light'>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Avatar</th>
                                        <th scope="col">Full Name</th>
                                        <th scope="col">email</th>
                                        <th scope="col">subscribed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <ListUsers listUsers={data} />
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

                </div>

            </div>

        </Col>
    )
}

export default Users