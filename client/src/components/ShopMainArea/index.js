import styles from './ShopMainArea.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { Link, useSearchParams } from 'react-router-dom';
import { GoSearch } from "react-icons/go";
import { useForm } from "react-hook-form";
import { useEffect, useState, createContext } from 'react'
import { products } from '../HotProduct/ProductWrapper/products';
import Cookies from 'js-cookie';
import stylesPaginated from '../usePagination/PaginatedItems.module.scss'
import usePaginate from '../usePagination/usePaginate';
import { formatter } from '../../utils/utils'
import axios from '../../service/axiosClient';
import ListProduct from './ListProduct';

function ShopMainArea() {
    const [searchParams] = useSearchParams();
    const [listProduct, setListProduct] = useState()
    const [search, setSearch] = useState('');
    const [listCategories, setListCategories] = useState([])
    const [category, setCategory] = useState('');
    const { register, handleSubmit } = useForm();
    const { register: register2 } = useForm();


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
            .get(`http://127.0.0.1:8000/api/products?${searchParams.toString()}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`,
                },
            })
            .then((response) => {
                setData({
                    data: response.data.data,
                    total: response.data.total,
                    page: response.data.current_page,
                    lastPage: response.data.last_page,
                    nextPage: response.data.current_page + 1,
                    prevPage: response.data.current_page - 1,
                });
            });
    }, [searchParams.toString()]);
    const handleSearch = (data) => {
        console.log(data.name)
        const payload = data.name.trim().replace(/\s/g, '%')
        axios
            .get(`http://127.0.0.1:8000/api/products/filter/search=${payload}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                console.log(response.data)
                setData({
                    data: response.data.data
                })
            })
            .catch(function (error) {
                console.log(error);
            });
    }
    return (
        <section id={styles.shopMainArea}>
            <Container fluid>
                <Row>
                    <Col lg={3}>
                        <div className={styles.shopSidebarWrapper}>
                            <div className={styles.shopSearch}>
                                <form onSubmit={handleSubmit(handleSearch)}>
                                    <input
                                        value={search}
                                        className="form-control"
                                        placeholder="Tìm kiếm..."
                                        {...register('name', { onChange: (e) => setSearch(e.target.value) })}
                                    />
                                    <button type="submit">
                                        <GoSearch />
                                    </button>
                                </form>
                            </div>
                            <div className={styles.shopSidebarBoxed}>
                                <h4>Danh Mục Sản Phẩm</h4>

                                <label className={styles.boxed}>Tất cả
                                    <input type="radio" name="radio"
                                        checked={category === "ALL" ? true : false}
                                        onChange={() => setCategory("ALL")}
                                    />
                                    <span className={styles.checkmark}></span>
                                </label>
                                <label className={styles.boxed}>Balo Tibi
                                    <input type="radio" name="radio" checked={category === "Tibi" ? true : false}
                                        onChange={() => setCategory("Tibi")} />
                                    <span className={styles.checkmark}></span>
                                </label>
                                <label className={styles.boxed}>Balo Laptop
                                    <input type="radio" name="radio" checked={category === "Laptop" ? true : false}
                                        onChange={() => setCategory("Laptop")} />
                                    <span className={styles.checkmark}></span>
                                </label>
                                <label className={styles.boxed}>Balo Tiểu học
                                    <input type="radio" name="radio" checked={category === "Tiểu học" ? true : false}
                                        onChange={() => setCategory("Tiểu học")} />
                                    <span className={styles.checkmark}></span>
                                </label>

                            </div>

                        </div>
                    </Col>
                    <Col lg={9}>
                        <ListProduct currentItems={data.data} />
                        < Col lg={12}>
                            <ul className={stylesPaginated.pagination}>
                                {data.page > 1 && <li className={stylesPaginated.pageItem}>
                                    <Link to={`?page=${data.prevPage}`} className={stylesPaginated.pageLink}>«</Link>
                                </li>}
                                {(data.page === data.lastPage && data.lastPage > 3) && <li className={stylesPaginated.pageItem}>
                                    <Link to={`?page=${1}`} className={stylesPaginated.pageLink}>1</Link>
                                </li>}
                                {(data.page === data.lastPage && data.lastPage > 3) && <li className={`${stylesPaginated.pageItem} ${stylesPaginated.disable}`}>
                                    <Link className={stylesPaginated.pageLink}>...</Link>
                                </li>}
                                {data.page - 1 > 0 && <li className={stylesPaginated.pageItem}><Link to={`?page=${data.prevPage}`} className={stylesPaginated.pageLink}>{data.page - 1}</Link></li>}

                                <li className={`${stylesPaginated.pageItem} ${stylesPaginated.active}`}>
                                    <Link to={`?page=${data.page}`} className={stylesPaginated.pageLink}>{data.page}</Link>
                                </li>
                                {data.page !== data.lastPage && <li className={stylesPaginated.pageItem}>
                                    <Link to={`?page=${data.nextPage}`} className={stylesPaginated.pageLink}>{data.page + 1}</Link>
                                </li>}
                                {/* {page - 1 === 0 && <li className={stylesPaginated.pageItem}><Link to={`?page=${page + 2}`} className={stylesPaginated.pageLink}>{page + 2}</Link></li>} */}
                                {data.page !== data.lastPage && <li className={`${stylesPaginated.pageItem} ${stylesPaginated.disable}`}>
                                    <Link className={stylesPaginated.pageLink}>...</Link>
                                </li>}
                                {data.page !== data.lastPage && <li className={stylesPaginated.pageItem}>
                                    <Link to={`?page=${data.lastPage}`} className={stylesPaginated.pageLink}>{data.lastPage}</Link>
                                </li>}
                                {data.page !== data.lastPage && <li className={stylesPaginated.pageItem}>
                                    <Link to={`?page=${data.nextPage}`} className={stylesPaginated.pageLink}>»</Link>
                                </li>}
                            </ul>
                        </Col>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default ShopMainArea