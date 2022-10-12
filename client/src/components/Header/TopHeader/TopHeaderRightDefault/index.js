import { FaUser, FaLock } from "react-icons/fa"
import styles from '../TopHeader.module.scss'
import { Link } from 'react-router-dom';

function TopHeaderRightDefault() {
    return (
        <div className={styles.divRight}>
            <ul className={styles.rightList}>
                <li>
                    <Link to="/login"><FaUser fontSize={12} /> Login</Link>
                </li>
                <li>
                    <Link to="/register"><FaLock fontSize={12} /> Register</Link>
                </li>
            </ul>
        </div>
    )
}

export default TopHeaderRightDefault