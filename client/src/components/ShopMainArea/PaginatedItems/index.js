import {
    useEffect,
    useState, useContext
} from "react";
import ReactPaginate from 'react-paginate'
import ListProduct from "../ListProduct";
import { ListProductContext } from '../index.js';
import Col from 'react-bootstrap/Col';
import styles from './PaginatedItems.module.scss'

function PaginatedItems({ itemsPerPage }) {

    const items = useContext(ListProductContext);

    const [currentItems, setCurrentItems] = useState([]);
    const [pageCount, setPageCount] = useState(0);
    const [itemOffset, setItemOffset] = useState(0);


    useEffect(() => {
        const endOffset = itemOffset + itemsPerPage;
        setCurrentItems(items.slice(itemOffset, endOffset));
        setPageCount(Math.ceil(items.length / itemsPerPage));
    }, [itemOffset, itemsPerPage, items]);

    const handlePageClick = (event) => {
        const newOffset = event.selected * itemsPerPage % items.length;
        setItemOffset(newOffset);
    };

    return (
        <>
            <ListProduct currentItems={currentItems} />
            <Col lg={12}>
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
        </>
    );
}

export default PaginatedItems