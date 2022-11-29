import { FaTruck, FaAngleDown, FaTachometerAlt, FaCubes, FaSignOutAlt } from 'react-icons/fa'
import styles from '../TopHeader.module.scss'
import { Link } from 'react-router-dom';
import Cookies from 'js-cookie';
import axios from '../../../../service/axiosClient';

function TopHeaderRightAuth(user) {

    const handleLogout = () => {
        axios
            .post('http://localhost:8000/api/logout', {},
                {
                    headers: {
                        Authorization: `Bearer ${Cookies.get('token')}`,
                    },
                },
            )
            .then(function (response) {
                if (response.data.success) {
                    Cookies.remove('token', { path: '/', domain: 'localhost' });
                    window.location.href = 'http://localhost:3000/login';
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
                    <img src={user.user.avatar ? user.user.avatar : user.user.defaultAvatar} alt="avatar" />{user.user.firstName} {user.user.lastName}
                    <FaAngleDown fontSize={12} />
                    <ul className={styles.dropDown}>
                        <li>
                            <Link to="/my-account"><FaTachometerAlt /> Dashboard</Link>
                        </li>
                        <li>
                            <Link to="/my-account/customer-order"><FaCubes /> My Orders</Link>
                        </li>
                        <li>
                            <button type='button' onClick={handleLogout}><FaSignOutAlt /> Log out</button>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    )
}

export default TopHeaderRightAuth