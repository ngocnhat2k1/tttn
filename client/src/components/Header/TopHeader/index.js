import styles from './TopHeader.module.css'
import {FaUser, FaSyncAlt, FaLock} from "react-icons/fa"

function TopHeader() {
    return (
        <div className={styles.divTopheader}>
            <div className={styles.divLeft}>
                <p>Special collection already available. <span className='colorOrange'>Read more...</span></p>
            </div>
            <div className={styles.divRight}>
                <ul>
                    <li className={styles.liRight}>
                    <a href=""><FaLock fontSize={13}/> Register</a>
                    </li>
                    <li className={styles.liRight}>
                    <a href=""><FaUser fontSize={13}/> Login</a>
                    </li>
                    <li className={styles.liRight}>
                    <a href=""><FaSyncAlt fontSize={13}/> Compare</a>
                    </li>
                    <div className={styles.clear}></div>
                </ul>
            </div>
            <div className={styles.clear}>
                
            </div>
        </div>
    )
}

export default TopHeader