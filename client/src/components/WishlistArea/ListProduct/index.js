import styles from '../Wishlist.module.css'
import { Link } from 'react-router-dom';
import { formatter } from '../../../utils/utils';
import { FaTrashAlt } from 'react-icons/fa';
import { useState, useEffect } from 'react';
import ModalNotifyAdd from './ModalNotifyAdd/index';
import ModalConfirm from './ModalConfirm';

function ListProduct(prop) {
    const [listWishlist, setListWishlist] = useState([]);

    useEffect(() => {
        setListWishlist(prop.list);
    }, [prop.list])

    return (
        <>
            {listWishlist.map((product, index) => {
                return (
                    <tr key={index}>
                        <td className={styles.productThumb}>
                            <Link to={`/shop/${product.id}`}>
                                <img src={product.img} alt="img" />
                            </Link>
                        </td>
                        <td className={styles.productName}>
                            <Link to={`/shop/${product.id}`}>
                                {product.name}
                            </Link>
                        </td>
                        <td className={styles.productPrice}>
                            {formatter.format(product.price * ((100 - product.percentSale) / 100))}
                        </td>
                        <td className={styles.productStock}>
                            <h6>{product.status === 1 ? 'Còn hàng' : 'Hết hàng'}</h6>
                        </td>
                        <td className={styles.productAddcart}>
                            <ModalNotifyAdd nameBtn='THÊM VÀO GIỎ HÀNG' productId={product.id} />
                        </td>
                        <td className={styles.productRemove}>
                            <ModalConfirm icon={<FaTrashAlt />} productId={product.id} />
                        </td>
                    </tr>
                )
            })
            }
        </>
    )
}

export default ListProduct