import styles from './CustomerAccountDetails.module.scss'
import { Link } from 'react-router-dom';
import { useEffect, useState } from 'react'
import axios from '../../../service/axiosClient'
import Cookies from 'js-cookie';

function CustomerAccountDetails() {

    const [profile, setProfile] = useState({
        avatar: '',
        firstName: '',
        lastName: '',
        email: '',
        phoneNumber: ''
    });

    useEffect(() => {
        axios
            .get(`http://localhost:8000/api/user/profile`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                setProfile(response.data.data);
            })
            .catch(function (error) {
                console.log(error);
            });
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
                            <img src={profile?.avatar} alt="img" />
                        </div>
                        <div className={styles.defaultFormBox}>
                            <label>First Name</label>
                            <input type="text" name="first-name" value={profile?.firstName} className='form-control' disabled />
                        </div>
                        <div className={styles.defaultFormBox}>
                            <label>Last Name</label>
                            <input type="text" name="last-name" value={profile?.lastName} className='form-control' disabled />
                        </div>
                        <div className={styles.defaultFormBox}>
                            <label>Email</label>
                            <input type="text" name="email-name" value={profile?.email} className='form-control' disabled />
                        </div>
                        <div className={styles.defaultFormBox}>
                            <label>Phone number</label>
                            <input type="text" name="phone-number" value={profile?.phoneNumber} className='form-control' disabled />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    )
}

export default CustomerAccountDetails