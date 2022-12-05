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
                                        Tất cả danh mục
                                    </h4>
                                    <Link data-toggle="tab" className="theme-btn-one bg-black btn_sm add_prod_button" to="/add-category">
                                        Thêm danh mục
                                    </Link>
                                </div>
                                <div className='table-responsive'>
                                    <table className='table pending_table'>
                                        <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Tên danh mục</th>
                                                <th scope="col">Chỉnh sửa</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <ListCategories currentCategory={data} />
                                        </tbody>
                                    </table>
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