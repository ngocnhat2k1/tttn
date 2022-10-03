import styles from './ContactInfo.module.css'
import Col from 'react-bootstrap/Col';
import { Info } from './Info';
import { MdLocationOn } from 'react-icons/md'
import { IoMdMail} from 'react-icons/io';
import { IoEarthSharp } from 'react-icons/io5';
import { FaPhoneAlt } from 'react-icons/fa'

function ContactInfo() {
    return (
        <Col lg={4}>
            <div className={styles.leftSideContact}>
                <ul>
                    <li className={styles.addressLocation}>
                        <div className={styles.contactWidget}>
                            <MdLocationOn className={styles.fixIcon}/>
                            <p>{Info.address}</p>
                        </div>
                    </li>
                    <li className={styles.addressLocation}>
                        <div className={styles.contactWidget}>
                            <FaPhoneAlt className={styles.icons}/>
                            <a href="">{Info.phone}</a>
                        </div>
                    </li>
                    <li className={styles.addressLocation}>
                        <div className={styles.contactWidget}>
                            <IoMdMail className={styles.icons}/>
                            <a href="">{Info.email}</a>
                        </div>
                        <div className={styles.contactWidget}>
                            <IoEarthSharp className={styles.icons}/>
                            <a href="">{Info.domain}</a>
                        </div>
                    </li>
                </ul>
            </div>
        </Col>
    )
}

export default ContactInfo