import styles from './CustomerAccountDetails.module.scss'
import {Link} from 'react-router-dom'
import BaoAvatar from '../../../images/Bao_avatar.jpg'
import { useEffect, useState } from 'react'
import axios from 'axios'
import Cookies from 'js-cookie';



function CustomerAccountDetails() {
    
    const [user, setUser] = useState();

    useEffect(() => {
        if(Cookies.get('token') !== undefined) {
        axios
            .get(`http://localhost:8000/api/user/profile`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                setUser(response.data.data);
            })
            .catch(function (error) {
                console.log(error);
            });
        }
    }, []);
    
    return (
        <div className={styles.myaccountContent}>
            <div className={`justify-content-between mt-3 d-flex align-items-center`}>
                <h4 className={styles.title}>Account details</h4>
                <Link to="/account-edit" className='theme-btn-one bg-black btn_sm'>UPDATE ACCOUNT</Link>
            </div>
            <div >
                <div className={styles.accountDetailsForm}>
                    <form>
                        <div className={styles.imgProfiles}>
                            <img src={BaoAvatar} alt="img" />
                        </div>
                        <div className={styles.defaultFormBox}>
                            <label>First Name</label>
                            <input type="text" name="first-name" value="Kazi" className='form-control' disabled/>
                        </div>
                        <div className={styles.defaultFormBox}>
                            <label>Last Name</label>
                            <input type="text" name="last-name" value="Saiful" className='form-control' disabled/>
                        </div>
                        <div className={styles.defaultFormBox}>
                            <label>Email</label>
                            <input type="text" name="email-name" value="test@gmail.com" className='form-control' disabled/>
                        </div>
                        <div className={styles.defaultFormBox}>
                            <label>Phone number</label>
                            <input type="text" name="phone-name" value="0969710601" className='form-control' disabled/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    )
}

export default CustomerAccountDetails