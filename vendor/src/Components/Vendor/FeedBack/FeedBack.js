import React from 'react'
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'

const FeedBack = () => {
    return (
        <Col sm={12} md={12} lg={9}>
            <div className='tab-content dashboard_content'>
                <div className='tab-pane fade show active'>
                    <Row>
                        <Col lg={12} md={12} sm={12} xs={12}>
                            <div className='vendor_order_boxed'>
                                <h4>Tất Cả Đánh Giá</h4>
                                <div className='table-resposive'>
                                    <table className='table pending_table'>
                                        <thead>
                                            <tr>
                                                <th scope='col'>Tên Khách Hàng</th>
                                                <th scope='col'>Tên Sản Phẩm</th>
                                                <th scope='col'>Nội Dung</th>
                                                <th scope='col'>Phone Receiver</th>
                                                <th scope='col'>Address</th>
                                                <th scope='col'>Status</th>
                                                <th scope='col'>Price</th>
                                                <th scope='col'>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {/* <ListOrder currentOrder={data.data} /> */}

                                        </tbody>
                                    </table>
                                    < Col lg={12}>
                                        {/* <ul className={styles.pagination}>
                                            {data.page > 1 && <li className={styles.pageItem}>
                                                <Link to={`?page=${data.prevPage}`} className={styles.pageLink}>«</Link>
                                            </li>}
                                            {data.page > 3 && <li className={styles.pageItem}>
                                                <Link to={`?page=${1}`} className={styles.pageLink}>1</Link>
                                            </li>}
                                            {data.page > 3 && <li className={`${styles.pageItem} ${styles.disable}`}>
                                                <Link className={styles.pageLink}>...</Link>
                                            </li>}
                                            {data.page === data.lastPage && <li className={styles.pageItem}>
                                                <Link to={`?page=${1}`} className={styles.pageLink}>1</Link>
                                            </li>}
                                            {data.page === data.lastPage && <li className={`${styles.pageItem} ${styles.disable}`}>
                                                <Link className={styles.pageLink}>...</Link>
                                            </li>}
                                            {data.page - 1 > 0 && <li className={styles.pageItem}><Link to={`?page=${data.prevPage}`} className={styles.pageLink}>{data.page - 1}</Link></li>}

                                            <li className={`${styles.pageItem} ${styles.active}`}>
                                                <Link to={`?page=${data.page}`} className={styles.pageLink}>{data.page}</Link>
                                            </li>
                                            {data.page !== data.lastPage && <li className={styles.pageItem}>
                                                <Link to={`?page=${data.nextPage}`} className={styles.pageLink}>{data.page + 1}</Link>
                                            </li>}
                                            {data.page - 1 === 0 && <li className={styles.pageItem}><Link to={`?page=${data.page + 2}`} className={styles.pageLink}>{data.page + 2}</Link></li>}
                                            {data.page !== data.lastPage && <li className={`${styles.pageItem} ${styles.disable}`}>
                                                <Link className={styles.pageLink}>...</Link>
                                            </li>}
                                            {data.page !== data.lastPage && <li className={styles.pageItem}>
                                                <Link to={`?page=${data.lastPage}`} className={styles.pageLink}>{data.lastPage}</Link>
                                            </li>}
                                            {data.page !== data.lastPage && <li className={styles.pageItem}>
                                                <Link to={`?page=${data.nextPage}`} className={styles.pageLink}>»</Link>
                                            </li>}

                                        </ul> */}

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

export default FeedBack