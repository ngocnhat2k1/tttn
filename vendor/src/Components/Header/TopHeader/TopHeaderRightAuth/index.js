import { FaTruck, FaAngleDown, FaTachometerAlt, FaCubes, FaSignOutAlt } from 'react-icons/fa'
import styles from '../TopHeader.module.scss'
import { Link } from 'react-router-dom';
import Cookies from 'js-cookie';
import axios from '../../../../service/axiosClient';

function TopHeaderRightAuth(user) {
    const handleLogout = () => {

        axios
            .post(
                'http://127.0.0.1:8000/api/admin/logout',
                {},
                {
                    headers: {
                        Authorization: `Bearer ${Cookies.get('token')}`,
                    },
                },
            )
            .then(function (response) {
                if (response.data.success) {
                    Cookies.remove('token', { path: '/' });
                    window.location.href = 'http://127.0.0.1:3000/login';
                } else {
                    console.log(response);
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    return (
        <div className={styles.divRight}>
            <ul className={styles.rightList}>
                <li>
                    <Link to="/order-tracking"><FaTruck fontSize={18} /> Track your Order</Link>
                </li>
                <li className={styles.account}>
                    <img src={user.user.avatar} alt="avatar" />{user.user.firstName} {user.user.lastName}
                    <FaAngleDown fontSize={12} />
                    <ul className={styles.dropDown}>
                        <li>
                            <Link to="/my-account"><FaTachometerAlt /> Dashboard</Link>
                        </li>
                        <li>
                            <Link to="/my-account/customer-order"><FaCubes /> My Orders</Link>
                        </li>
                        <li>
                            <button onClick={handleLogout}><FaSignOutAlt /> Log out</button>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    )
}

export default TopHeaderRightAuth