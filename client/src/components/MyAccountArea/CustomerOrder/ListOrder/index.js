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
            {listOrder && listOrder.map((order, index) => {
                return (
                    <tr key={index}>
                        <td>{order.idDelivery}</td>
                        <td>{order.dateOrder}</td>
                        <td>{order.nameReceiver}</td>
                        <td className={styles.status}>


                            {order.status}

                        </td>
                        <td>{formatter.format(order.totalPrice)}</td>
                        <td className={styles.action}>
                            <Link className={styles.view} to={`/order-detail/${order.idDelivery}`}>View</Link>
                        </td>
                    </tr>
                )
            })}
        </>
    )
}

export default ListOrder;