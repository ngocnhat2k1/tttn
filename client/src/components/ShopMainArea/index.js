import styles from './ShopMainArea.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { GoSearch } from "react-icons/go";
import { useEffect, useState, createContext } from 'react'
import { products } from '../HotProduct/ProductWrapper/products';
import { formatter } from '../../utils/utils'
import PaginatedItems from './PaginatedItems';
import axios from '../../service/axiosClient';

export const ListProductContext = createContext();

function ShopMainArea() {

    const [search, setSearch] = useState('');
    const [category, setCategory] = useState('ALL');
    const [price, setPrice] = useState(100000);
    const [gender, setGender] = useState('ALL');
    const [listProduct, setListProduct] = useState(products);
    //Test API
    // const [listAPI, setListAPI] = useState([]);

    // useEffect(() => {
    //     const apiListProduct = async () => {
    //         const data = await axios.get(`http://localhost:8000/api/v1/products`);
    //         return data;
    //     };
    //     apiListProduct()
    //         .then((response) => {
    //             setListAPI(response.data);
    //         })
    //         .catch(function (error) {
    //             console.log(error);
    //         });
    // }, []);

    // console.log(listAPI)

    const handlePriceFilter = (e) => {
        setPrice(e.target.value);
    }

    const handleClear = () => {
        setPrice(100000);
        setGender('ALL');
        setCategory('ALL');
        setSearch('');
        setListProduct(products)
    }

    useEffect(() => {
        let List = products;

        if (search !== "") {
            List = List.filter(product => { return product.name.toLocaleLowerCase().includes(search.toLocaleLowerCase()) })
        }

        List = List.filter(product => { return product.discount !== "" ? product.cost * ((100 - product.discount) / 100) >= price : product.cost >= price })

        if (category !== "ALL") {
            List = List.filter(product => { return product.category === category })
        }

        if (gender !== "ALL") {
            List = List.filter(product => { return product.gender === gender })
        }

        setListProduct(List);
    }, [price, search, category, gender])

    return (
        <ListProductContext.Provider value={listProduct}>
        <section id={styles.shopMainArea}>
            <Container fluid>
                <Row>
                    <Col lg={3}>
                        <div className={styles.shopSidebarWrapper}>
                            <div className={styles.shopSearch}>
                                <form>
                                    <input value={search} className="form-control" placeholder="Search..."
                                        onChange={(e) => setSearch(e.target.value)}
                                    />
                                    <button type="">
                                        <GoSearch />
                                    </button>
                                </form>
                            </div>
                            <div className={styles.shopSidebarBoxed}>
                                <h4>Product Categories</h4>
                                <form>
                                    <label className={styles.boxed}>ALL
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
                                </form>
                            </div>
                            <div className={styles.shopSidebarBoxed}>
                                <h4>Gender</h4>
                                <form>
                                    <label className={styles.boxed}>ALL
                                        <input type="radio" name="radio"
                                            checked={gender === "ALL" ? true : false}
                                            onChange={() => setGender("ALL")}
                                        />
                                        <span className={styles.checkmark}></span>
                                    </label>
                                    <label className={styles.boxed}>Boy
                                        <input type="radio" name="radio" checked={gender === "Boy" ? true : false}
                                            onChange={() => setGender("Boy")} />
                                        <span className={styles.checkmark}></span>
                                    </label>
                                    <label className={styles.boxed}>Girl
                                        <input type="radio" name="radio" checked={gender === "Girl" ? true : false}
                                            onChange={() => setGender("Girl")} />
                                        <span className={styles.checkmark}></span>
                                    </label>
                                </form>
                            </div>
                            <div className={styles.shopSidebarBoxed}>
                                <h4>Price</h4>
                                <div className={styles.priceFilter}>
                                    <input id={styles.formControlRange} type="range" onInput={handlePriceFilter} min="100000" max="500000" value={price} />
                                    <div className={styles.price}>
                                        <span>Price: {formatter.format(price)}</span>
                                    </div>
                                </div>
                            </div>
                            <div className={styles.clearButton}>
                                <button type="button" onClick={handleClear}>CLEAR FILTER</button>
                            </div>
                        </div>
                    </Col>
                    <Col lg={9}>
                        <Row>
                        <PaginatedItems itemsPerPage={12}/>
                        </Row>
                    </Col>
                </Row>
            </Container>
        </section>
    </ListProductContext.Provider>
    )
}

export default ShopMainArea