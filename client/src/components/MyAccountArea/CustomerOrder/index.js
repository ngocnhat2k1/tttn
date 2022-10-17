import styles from '../MyAccountArea.module.scss';
import { formatter } from '../../../utils/utils';
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
                                    <th>Status by</th>
                                    <th>Total</th>
                                    <th colSpan={2}>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>May 10, 2018</td>
                                    <td><span className={`${styles.badge} ${styles.badgeGreen}`}>Completed</span></td>
                                    <td><strong>Admin:</strong> Lê Quốc Bảo</td>
                                    <td>{formatter.format(200000)}</td>
                                    <td>
                                        <a className={styles.view} href="">View</a>
                                    </td>
                                    <td>
                                        <button type="button" className={styles.btnDeleteOrder}>Hủy đơn</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
    )
}

export default CustomerOrder