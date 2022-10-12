import { FaTruck, FaAngleDown, FaTachometerAlt, FaCubes, FaSignOutAlt } from 'react-icons/fa'
import styles from '../TopHeader.module.scss'
import { Link } from 'react-router-dom';
import BaoAvatar from '../../../../images/Bao_avatar.jpg';
import Cookies from 'js-cookie';

function TopHeaderRightAuth() {

    const handleLogout = () => {
        Cookies.remove('token', { path: '/'});
        window.location.href = 'http://localhost:3000/login';
        // axios
        //     .post(
        //         'http://localhost:8080/tttn_be/public/api/user/logout',
        //         {},
        //         {
        //             headers: {
        //                 Authorization: `Bearer ${cookies.get('token')}`,
        //             },
        //         },
        //     )
        //     .then(function (response) {
        //         if (response.data.result) {
        //             removeCookie('token');
        //             window.location.href = 'http://localhost:3000/login';
        //         } else {
        //             console.log(response);
        //         }
        //     })
        //     .catch(function (error) {
        //         console.log(error);
        //     });
    }

    return (
        <div className={styles.divRight}>
            <ul className={styles.rightList}>
                <li>
                    <Link to="/order-tracking"><FaTruck fontSize={18} /> Track your Order</Link>
                </li>
                <li className={styles.account}>
                    <img src={BaoAvatar} alt="avatar" />Lê Quốc Bảo
                    <FaAngleDown fontSize={12} />
                    <ul className={styles.dropDown}>
                        <li>
                            <Link to="/my-account"><FaTachometerAlt /> Dashboard</Link>
                        </li>
                        <li>
                            <Link to="/my-account/customer-order"><FaCubes /> My Orders</Link>
                        </li>
                        <li>
                            <Link to="/login" onClick={handleLogout}><FaSignOutAlt /> Log out</Link>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    )
}

export default TopHeaderRightAuth