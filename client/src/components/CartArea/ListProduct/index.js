import styles from '../Cart.module.scss'
import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom';
import { FaTrashAlt } from 'react-icons/fa';
import { formatter } from '../../../utils/utils';

function ListProduct(prop) {
    const [listProduct, setListProduct] = useState([]);

    useEffect(() => {
        setListProduct(prop.list);
    }, [prop.list]);

    const handleDeleteProduct = () => {
    }

    return (
        <>
            {
                listProduct.map((product, index) => {
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
                                {formatter.format(product.price * ((100 - product.precentSale) / 100))}
                            </td>
                            <td className={styles.productQuantity}>
                                <input type="number" defaultValue={product.quantity} min="1" max="5" />
                            </td>
                            <td className={styles.productTotal}>{formatter.format(product.price * ((100 - product.precentSale) / 100) * product.quantity)}</td>
                            <td className={styles.productRemove} onClick={handleDeleteProduct}><FaTrashAlt /></td>
                        </tr>
                    )
                })
            }
        </>
    )
}

export default ListProduct;