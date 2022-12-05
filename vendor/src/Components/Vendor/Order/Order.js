import React, { useEffect, useState } from 'react'
import { Link, useSearchParams } from "react-router-dom";
import 'bootstrap/dist/css/bootstrap.min.css';
import Cookies from 'js-cookie';
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import axios from 'axios';
import '../DashBoard.css'
import ListOrder from './ListOrder/ListOrder';
import styles from '../../Hook/usePagination/PaginatedItems.module.scss'

const Order = () => {
    const [searchParams] = useSearchParams();
    const [data, setData] = useState({
        data: [],
        page: 0,
        nextPage: 0,
        prevPage: 0,
        lastPage: 0,
        total: 0,
    });
    useEffect(() => {
        axios
            .get(`http://127.0.0.1:8000/api/v1/orders?${searchParams.toString()}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`,
                },
            })

            .then((response) => {
                if (response.data.success === undefined) {
                    setData({
                        data: response.data.data,
                        total: response.data.total,
                        page: response.data.current_page,
                        lastPage: response.data.last_page,
                        nextPage: response.data.current_page + 1,
                        prevPage: response.data.current_page - 1,
                    });
                }
            });
    }, [searchParams.toString()]);

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
                                                <th scope='col'>Mã đơn hàng</th>
                                                <th scope='col'>Tên tài khoản</th>
                                                <th scope='col'>Tên người nhận</th>
                                                <th scope='col'>Số điện thoại người nhận</th>
                                                <th scope='col'>Địa chỉ</th>
                                                <th scope='col'>Trạng thái</th>
                                                <th scope='col'>Giá</th>
                                                <th scope='col'>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <ListOrder currentOrder={data.data} />

                                        </tbody>
                                    </table>
                                    < Col lg={12}>
                                        <ul className={styles.pagination}>
                                            {data.page > 1 && <li className={styles.pageItem}>
                                                <Link to={`?page=${data.prevPage}`} className={styles.pageLink}>«</Link>
                                            </li>}
                                            {data.page > 4 && <li className={styles.pageItem}>
                                                <Link to={`?page=${1}`} className={styles.pageLink}>1</Link>
                                            </li>}
                                            {data.page > 4 && <li className={`${styles.pageItem} ${styles.disable}`}>
                                                <Link className={styles.pageLink}>...</Link>
                                            </li>}
                                            {data.page - 1 > 0 && <li className={styles.pageItem}><Link to={`?page=${data.prevPage}`} className={styles.pageLink}>{data.page - 1}</Link></li>}

                                            <li className={`${styles.pageItem} ${styles.active}`}>
                                                <Link to={`?page=${data.page}`} className={styles.pageLink}>{data.page}</Link>
                                            </li>
                                            {data.page !== data.lastPage && <li className={styles.pageItem}>
                                                <Link to={`?page=${data.nextPage}`} className={styles.pageLink}>{data.page + 1}</Link>
                                            </li>}
                                            {data.page - 3 === 0 && <li className={styles.pageItem}><Link to={`?page=${data.page + 2}`} className={styles.pageLink}>{data.page + 2}</Link></li>}
                                            {data.page !== data.lastPage && <li className={`${styles.pageItem} ${styles.disable}`}>
                                                <Link className={styles.pageLink}>...</Link>
                                            </li>}
                                            {data.page !== data.lastPage && <li className={styles.pageItem}>
                                                <Link to={`?page=${data.lastPage}`} className={styles.pageLink}>{data.lastPage}</Link>
                                            </li>}
                                            {data.page !== data.lastPage && <li className={styles.pageItem}>
                                                <Link to={`?page=${data.nextPage}`} className={styles.pageLink}>»</Link>
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