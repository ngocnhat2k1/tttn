import styles from './AccountEditArea.module.scss';
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { Link } from 'react-router-dom';
import { FaArrowLeft, FaCamera } from 'react-icons/fa';
import BaoAvatar from '../../images/Bao_avatar.jpg';
import axios from '../../service/axiosClient';
import { useState, useEffect } from 'react';
import Cookies from 'js-cookie';

function AccountEditArea() {

    const [lastName, setLastName] = useState('');
    const [firstName, setFirstName] = useState('');
    const [email, setEmail] = useState('');
    const [avatar, setAvatar] = useState('');
    const [password, setPassword] = useState('');
    const [newPassword, setNewPassword] = useState('');
    const [confirmNewPassword, setConfirmNewPassword] = useState('');
    const [errors, setError] = useState({
        firstName: false,
        lastName: false,
        email: false,
        avatar: false,
        password: false,
        newPassword: false,
        confirmNewPassword: false,
    });
    const regExp = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;

    useEffect(() => {
        axios
            .get(`http://localhost:8000/api/user/profile`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                setLastName(response.data.data.lastName);
                setFirstName(response.data.data.firstName);
                setEmail(response.data.data.email);
                setAvatar(response.data.data.avatar)
            })
            .catch(function (error) {
                console.log(error);
            });
    }, []);

    const handleUpdateName = () => {
        if (firstName === '' || firstName.length < 2 || firstName.length > 50) {
            setError((prevState) => ({
                ...prevState,
                firstName: true,
            }));
        } else {
            setError((prevState) => ({
                ...prevState,
                firstName: false,
            }));
        }

        if (lastName === '' || lastName.length < 2 || firstName.length > 50) {
            setError((prevState) => ({
                ...prevState,
                lastName: true,
            }));
        } else {
            setError((prevState) => ({
                ...prevState,
                lastName: false,
            }));
        }
    }

    const handleUpdateEmail = () => {
        if (!email.match(regExp) || email === '') {
            setError((prevState) => ({
                ...prevState,
                email: true,
            }));
        } else {
            setError((prevState) => ({
                ...prevState,
                email: false,
            }));
        }
    }

    const handleUpdatePassword = () => {
        if (password === '' || password.length < 6 || password.length > 24) {
            setError((prevState) => ({
                ...prevState,
                password: true,
            }));
        } else {
            setError((prevState) => ({
                ...prevState,
                password: false,
            }));
        }

        if (newPassword === '' || newPassword.length < 6 || newPassword.length > 24) {
            setError((prevState) => ({
                ...prevState,
                newPassword: true,
            }));
        } else {
            setError((prevState) => ({
                ...prevState,
                newPassword: false,
            }));
        }

        if (confirmNewPassword !== newPassword) {
            setError((prevState) => ({
                ...prevState,
                confirmNewPassword: true,
            }));
        } else {
            setError((prevState) => ({
                ...prevState,
                confirmNewPassword: false,
            }));
        }
    }

    return (
        <section id={styles.accountEdit} className='ptb100'>
            <Container fluid>
                <Row>
                    <Col lg={6}>
                        <div className={styles.backBtn}>
                            <Link to='/my-account/customer-account-details'><FaArrowLeft /> Back to Dashboard</Link>
                        </div>
                    </Col>
                </Row>
                <Row>
                    <Col lg={3}>
                        <div className={styles.accountThumd}>
                            <div className={styles.accountThumbImg}>
                                <img src={BaoAvatar} alt="img" />
                                <div className={styles.fixedIcon}>
                                    <input type="file" accept='image/*' /><FaCamera />
                                </div>
                            </div>
                            <h4>Lê Quốc Bảo</h4>
                        </div>
                    </Col>
                    <Col lg={9}>
                        <div className={styles.accountSetting}>
                            <div className={styles.accountSettingHeading}>
                                <h2>Account Details</h2>
                                <p>Edit your account settings and change your password here.</p>
                            </div>
                            <form id={styles.accountEditForm}>
                                <div className={styles.formGroup}>
                                    <label htmlFor="firstName">
                                        First Name
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        value={firstName}
                                        onChange={e => setFirstName(e.target.value)}
                                        required
                                    />
                                    {errors["firstName"] && (
                                        <p className="checkInput">Invalid First Name</p>
                                    )}
                                    <label htmlFor="lastName">
                                        Last Name
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        value={lastName}
                                        onChange={e => setLastName(e.target.value)}
                                        required
                                    />
                                    {errors["lastName"] && (
                                        <p className="checkInput">Invalid Last Name</p>
                                    )}
                                    <button type="button" className='theme-btn-one bg-black btn_sm' onClick={handleUpdateName}>Update Name</button>
                                </div>
                                <div className={styles.formGroup}>
                                    <label htmlFor="email">Email Address
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="email"
                                        value={email}
                                        onChange={e => setEmail(e.target.value)}
                                        required
                                    />
                                    {errors["email"] && (
                                        <p className="checkInput">Invalid Email</p>
                                    )}
                                    <button type="button" className='theme-btn-one bg-black btn_sm' onClick={handleUpdateEmail}>Update Email</button>
                                </div>
                                <div className={styles.formGroup}>
                                    <label htmlFor="password">Current Password
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="password"
                                        value={password}
                                        onChange={e => setPassword(e.target.value)}
                                        required
                                    />
                                    {errors["password"] && (
                                        <p className="checkInput">Invalid Password</p>
                                    )}
                                    <label htmlFor="newPassword">New Password
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="password"
                                        value={newPassword}
                                        onChange={e => setNewPassword(e.target.value)}
                                        required
                                    />
                                    {errors["newPassword"] && (
                                        <p className="checkInput">Invalid Password</p>
                                    )}
                                    <label htmlFor="confirmNewPassword">Password
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="password"
                                        value={confirmNewPassword}
                                        onChange={e => setConfirmNewPassword(e.target.value)}
                                        required
                                    />
                                    {errors["confirmNewPassword"] && (
                                        <p className="checkInput">Those passwords didn't match. Try again.</p>
                                    )}
                                    <button type="button" className='theme-btn-one bg-black btn_sm' onClick={handleUpdatePassword}>Update Password</button>
                                </div>
                            </form>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default AccountEditArea;