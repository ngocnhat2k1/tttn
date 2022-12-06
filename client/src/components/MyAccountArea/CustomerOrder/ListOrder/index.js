import styles from '../../MyAccountArea.module.scss'
import { formatter } from '../../../../utils/utils';
import axios from 'axios';
import { useState, useEffect } from 'react';
import Cookies from 'js-cookie';
import { Link } from 'react-router-dom'

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

    return (
        <>
            {listOrder.map((order, index) => {
                return (
                    <tr key={index}>
                        <td>{order.idDelivery}</td>
                        <td>{order.dateOrder}</td>
                        <td>{order.nameReceiver}</td>
                        <td className={styles.status}>
                            <span className={`${styles.badge}
                        ${order.deletedBy !== null ? styles.badgeCanceled : order.status === 0 ? styles.badgePending : order.status === 1 ? styles.badgeConfirm : styles.badgeCompleted}`}>
                                {order.deletedBy !== null ? 'Đã hủy' : order.status === 0 ? 'Đang chờ' : order.status === 1 ? 'Đã xác nhận' : 'Đã hoàn thành'}</span>
                        </td>
                        <td>{formatter.format(order.totalPrice)}</td>
                        <td className={styles.action}>
                            <Link className={styles.view} to={`/order-detail/${order.id}`}>View</Link>
                        </td>
                    </tr>
                )
            })}
        </>
    )
}

export default ListOrder;