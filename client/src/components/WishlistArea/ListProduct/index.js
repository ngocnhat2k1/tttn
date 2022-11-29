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

    const handleDeleteProduct = () => {

    }

    return (
        <>
            {listWishlist.map((product, index) => {
                return (
                    <tr key={index}>
                        <td className={styles.productThumb}>
                            <Link>
                                <img src={product.img} alt="img" />
                            </Link>
                        </td>
                        <td className={styles.productName}>
                            <Link>
                                {product.name}
                            </Link>
                        </td>
                        <td className={styles.productPrice}>
                            {formatter.format(product.price * ((100 - product.percentSale) / 100))}
                        </td>
                        <td className={styles.productStock}>
                            <h6>{product.status === 1 ? 'In stock' : 'Out of stock'}</h6>
                        </td>
                        <td className={styles.productAddcart}>
                            {/* <button type="button" className='theme-btn-one btn-black-overlay btn_sm' onClick={() => handleAddToCart(product.id)}>ADD TO CART</button> */}
                            <ModalNotifyAdd nameBtn='ADD TO CART' productId={product.id}/>
                        </td>
                        <td className={styles.productRemove} onClick={handleDeleteProduct}>
                            <ModalConfirm icon={<FaTrashAlt />} productId={product.id}/>
                        </td>
                    </tr>
                )
            })
            }
        </>
    )
}

export default ListProduct