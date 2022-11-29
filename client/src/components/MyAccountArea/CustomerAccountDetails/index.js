import styles from './CustomerAccountDetails.module.scss'
import { Link } from 'react-router-dom';
import { useEffect, useState } from 'react'
import axios from '../../../service/axiosClient'
import Cookies from 'js-cookie';
import MessageModal from './MessageModal/index'

function CustomerAccountDetails() {

    const [lastName, setLastName] = useState('');
    const [firstName, setFirstName] = useState('');
    const [email, setEmail] = useState('');
    const [avatar, setAvatar] = useState('');
    const [subscribe, setSubscribe] = useState('');

    useEffect(() => {
        axios
            .get(`http://localhost:8000/api/user/profile`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                setFirstName(response.data.data.firstName);
                setLastName(response.data.data.lastName);
                setEmail(response.data.data.email);
                if (!response.data.data.avatar) {
                    setAvatar(response.data.data.defaultAvatar)
                } else {
                    setAvatar(response.data.data.avatar)
                }
                setSubscribe(response.data.data.subscribe);
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
                            <img src={avatar} alt="img" />
                        </div>
                        <div className={styles.defaultFormBox}>
                            <label>First Name</label>
                            <input type="text" name="first-name" value={firstName} className='form-control' disabled />
                        </div>
                        <div className={styles.defaultFormBox}>
                            <label>Last Name</label>
                            <input type="text" name="last-name" value={lastName} className='form-control' disabled />
                        </div>
                        <div className={styles.defaultFormBox}>
                            <label>Email</label>
                            <input type="text" name="email-name" value={email} className='form-control' disabled />
                        </div>
                        <MessageModal subsc={subscribe} />
                    </form>
                </div>
            </div>
        </div>
    )
}

export default CustomerAccountDetails