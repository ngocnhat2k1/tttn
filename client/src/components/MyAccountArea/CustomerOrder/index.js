import styles from '../MyAccountArea.module.scss';
import ListOrder from './ListOrder';

function CustomerOrder() {
    return (
        <div className={styles.myaccountContent}>
            <h4 className={styles.title}>Đơn hàng</h4>
            <div className={`${styles.tableResponsive} ${styles.tablePage}`}>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ngày đặt</th>
                            <th>Người nhận</th>
                            <th>Tình trạng</th>
                            <th>Tổng tiền</th>
                            <th>Hành động</th>
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