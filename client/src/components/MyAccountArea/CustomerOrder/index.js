import styles from '../MyAccountArea.module.scss';
import ListOrder from './ListOrder';

function CustomerOrder() {
    return (
        <div className={styles.myaccountContent}>
            <h4 className={styles.title}>Orders</h4>
            <div className={`${styles.tableResponsive} ${styles.tablePage}`}>
                <table>
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <ListOrder />
                    </tbody>
                </table>
            </div>
        </div>
    )
}

export default CustomerOrder