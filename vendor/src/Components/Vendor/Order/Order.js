import React from 'react'
import 'bootstrap/dist/css/bootstrap.min.css';
import { useEffect, useState, createContext } from 'react'
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import ReactPaginate from 'react-paginate'
import '../DashBoard.css'
import './Order.css'
import { FakeOrders } from '../FakeData/FakeOrders.js'
import styles from './PaginatedItems.module.scss'
import ListOrder from './ListOrder/ListOrder';

export const ListOrderContext = createContext();

const Oder = () => {
    const [listOrder, setListOrder] = useState(FakeOrders)
    const [currentOrders, setCurrentItems] = useState([]);

    const itemsPerPage = 10;

    const [pageCount, setPageCount] = useState(0);
    const [itemOffset, setItemOffset] = useState(0);
    useEffect(() => {
        const endOffset = itemOffset + itemsPerPage;
        setCurrentItems(listOrder.slice(itemOffset, endOffset));
        setPageCount(Math.ceil(listOrder.length / itemsPerPage));
    }, [itemOffset, itemsPerPage, listOrder]);
    const handlePageClick = (event) => {
        const newOffset = event.selected * itemsPerPage % listOrder.length;
        setItemOffset(newOffset);
    };
    return (
        <ListOrderContext.Provider value={listOrder}>
            <Col sm={12} md={12} lg={9}>
                <div className='tab-content dashboard_content'>
                    <div className='tab-pane fade show active'>
                        <Row>
                            <Col lg={12} md={12} sm={12} xs={12}>
                                <div className='vendor_order_boxed'>
                                    <h4>All order</h4>
                                    <div className='table-resposive'>
                                        <table className='table pending_table'>
                                            <thead className='thead-light'>
                                                <tr>
                                                    <th scope='col'>Order Id</th>
                                                    <th scope='col'>Product Detail</th>
                                                    <th scope='col'>Status</th>
                                                    <th scope='col'>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <ListOrder currentOrder={currentOrders} />

                                            </tbody>
                                        </table>
                                        < Col lg={12}>
                                            <ReactPaginate
                                                nextLabel="»"
                                                onPageChange={handlePageClick}
                                                pageRangeDisplayed={3}
                                                marginPagesDisplayed={2}
                                                pageCount={pageCount}
                                                previousLabel="«"
                                                pageClassName={styles.pageItem}
                                                pageLinkClassName={styles.pageLink}
                                                previousClassName={styles.pageItem}
                                                previousLinkClassName={styles.pageLink}
                                                nextClassName={styles.pageItem}
                                                nextLinkClassName={styles.pageLink}
                                                breakLabel="..."
                                                breakClassName={styles.pageItem}
                                                breakLinkClassName={styles.pageLink}
                                                containerClassName={styles.pagination}
                                                activeClassName={styles.active}
                                                renderOnZeroPageCount={null}
                                            />
                                        </Col>


                                    </div>

                                </div>
                            </Col>
                        </Row>
                    </div>

                </div>
            </Col>
        </ListOrderContext.Provider >
    )
}

export default Oder