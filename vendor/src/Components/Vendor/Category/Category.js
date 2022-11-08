import React from 'react'
import Col from 'react-bootstrap/Col';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../DashBoard.css'

const Category = () => {
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
                                        <th scope="col">Email</th>
                                        <th scope="col">Subscribed</th>
                                    </tr>
                                </thead>
                                {/* <tbody>
                                    <ListUsers listUsers={data} />
                                </tbody> */}
                            </table>
                            {/* < Col lg={12}>
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
                            </Col> */}

                        </div>
                    </div>

                </div>

            </div>
        </Col>
    )
}

export default Category