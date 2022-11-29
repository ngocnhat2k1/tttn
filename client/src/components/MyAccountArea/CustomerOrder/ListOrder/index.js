import styles from '../../MyAccountArea.module.scss'
import { formatter } from '../../../../utils/utils';
import axios from 'axios';
import { useState, useEffect } from 'react';
import Cookies from 'js-cookie';

function ListOrder() {

    const [listOrder, setListOrders] = useState([]);

    useEffect(() => {
        axios
            .get(`http://localhost:8000/api/user/order/`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then(response => {
                setListOrders(response.data.data);
            })
            .catch(error => {
                console.log(error);
            });
    }, []);

    console.log(listOrder)

    return (
        <>
            {listOrder.map((order, index) => {
                return (
                    <tr key={index}>
                        <td>{index + 1}</td>
                        <td>{order.dateOrder}</td>
                        <td>
                            <span className={`${styles.badge}
                        ${order.deletedBy !== null ? styles.badgeCanceled : order.status === 0 ? styles.badgePending : styles.badgeCompleted}`}>
                                {order.deletedBy !== null ? 'Cancelled' : order.status === 0 ? 'Pending' : order.status === 1 ? 'Confirm' : 'Completed'}</span>
                        </td>
                        <td>{formatter.format(order.totalPrice)}</td>
                        <td>
                            <a className={styles.view} href="">View</a>
                        </td>
                    </tr>
                )
            })}
        </>
    )
}

export default ListOrder;