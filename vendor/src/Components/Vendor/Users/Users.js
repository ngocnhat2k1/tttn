import React, { useEffect, useState } from 'react'
import { Link, useSearchParams } from 'react-router-dom';
import 'bootstrap/dist/css/bootstrap.min.css';
import axios from 'axios';
import Col from 'react-bootstrap/Col';
import ListUsers from './ListUsers/ListUsers';
import styles from '../../Hook/usePagination/PaginatedItems.module.scss'
import Cookies from 'js-cookie';
import '../DashBoard.css'

const Users = () => {
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
            .get(`http://127.0.0.1:8000/api/v1/users?${searchParams.toString()}`, {
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
                    <div className='vendors_profiles'>
                        <h4>All Users</h4>

                        <div className='table-responsive'>
                            <table className='table pending_table'>
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Ảnh đại diện</th>
                                        <th scope="col">Họ và tên</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <ListUsers listUsers={data.data} />
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
                    </div>

                </div>

            </div>

        </Col>
    )
}

export default Users