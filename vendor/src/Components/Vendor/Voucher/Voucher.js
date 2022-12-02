import React, { useState, useEffect } from 'react'
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import { Link, useSearchParams } from "react-router-dom";
import 'bootstrap/dist/css/bootstrap.min.css';
import ListVoucher from './ListVoucher/ListVoucher'
import '../DashBoard.css'
import axios from 'axios';
import Cookies from 'js-cookie';
import styles from '../../Hook/usePagination/PaginatedItems.module.scss'

const Voucher = () => {
    const [data, setData] = useState({
        data: [],
        page: 0,
        nextPage: 0,
        prevPage: 0,
        lastPage: 0,
        total: 0,
    });
    const [searchParams] = useSearchParams();

    useEffect(() => {
        axios
            .get(`http://127.0.0.1:8000/api/v1/vouchers?${searchParams.toString()}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`,
                },
            })

            .then((response) => {
                setData({
                    data: response.data.data,
                    total: response.data.total,
                    page: response.data.meta.current_page,
                    lastPage: response.data.meta.last_page,
                    nextPage: response.data.meta.current_page + 1,
                    prevPage: response.data.meta.current_page - 1,
                });
            });
    }, [searchParams.toString()]);
    return (
        <Col sm={12} md={12} lg={9}>
            <div className='tab-content dashboard_content'>
                <div className='tab-pane fade show active'>
                    <div className='vendors_profiles'>
                        <Row>
                            <Col lg={12} md={12} sm={12} xs={12} className='position-relative'>
                                <div className='vendors_profiles pt-4'>
                                    <div className='mb-2'>
                                        <h4>
                                            All Voucher
                                        </h4>
                                        <Link data-toggle="tab" className="theme-btn-one bg-black btn_sm add_prod_button" to="/add-voucher">
                                            Add Voucher
                                        </Link>
                                    </div>
                                </div>
                                <div className='table-responsive'>
                                    <table className='table pending_table'>
                                        <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Usage</th>
                                                <th scope="col">Percent Sale</th>
                                                <th scope="col">Expired Date</th>
                                                <th scope='col'>Status</th>
                                                <th scope="col">Edit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <ListVoucher currentVoucher={data.data} />
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
                                        </ul>
                                    </Col>
                                </div>
                            </Col>
                        </Row>
                    </div>
                </div>
            </div>
        </Col>
    )
}

export default Voucher