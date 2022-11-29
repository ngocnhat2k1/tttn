import styles from './AccountEditArea.module.css';
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { Link } from 'react-router-dom';
import { FaArrowLeft, FaCamera } from 'react-icons/fa';
import { useForm } from "react-hook-form";
import axios from '../../service/axiosClient';
import { useState, useEffect } from 'react';
import Cookies from 'js-cookie';
import AccountEditModal from './AccountEditModal';

function AccountEditArea() {

    const [lastName, setLastName] = useState('');
    const [firstName, setFirstName] = useState('');
    const [email, setEmail] = useState('');
    const [avatar, setAvatar] = useState('');
    const { register, handleSubmit, formState: { errors }, reset } = useForm({
        defaultValues: {
            first_name: firstName,
            last_name: lastName,
            email: email,
            avatar: avatar
        }
    });
    const [message, setMessage] = useState('');
    const [success, setSuccess] = useState('');

    const { register: register2, handleSubmit: handleSubmit2, formState: { errors: errors2 }, reset: reset2 } = useForm();

    const { register: register3, handleSubmit: handleSubmit3, formState: { errors: errors3 } } = useForm();

    useEffect(() => {
        axios
            .get(`http://localhost:8000/api/user/profile`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                reset(response.data.data);
                setFirstName(response.data.data.firstName);
                setLastName(response.data.data.lastName);
                setEmail(response.data.data.email);
                if (!response.data.data.avatar) {
                    setAvatar(response.data.data.defaultAvatar)
                } else {
                    setAvatar(response.data.data.avatar)
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }, []);

    const handleUpdateInformation = (data) => {
        let { id, createdAt, defaultAvatar, avatar, disabled, subscribed, updatedAt, ...rest } = data;

        axios
            .put(`http://localhost:8000/api/user/update`, rest, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then(function (response) {
                if (response.data.success) {
                    setSuccess(response.data.success)
                    setMessage(response.data.message)
                } else {
                    setSuccess(response.data.success)
                    setMessage(response.data.message);
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    };

    const handleUpdatePassword = (data) => {
        console.log(data);

        axios
            .put(`http://localhost:8000/api/user/changePassword`, data, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then(function (response) {
                if (response.data.success) {
                    setSuccess(response.data.success)
                    setMessage(response.data.message)
                    reset2()
                } else {
                    setSuccess(response.data.success)
                    setMessage(response.data.message);
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    const handleImage = (e) => {
        const file = e.target.files[0];

        const Reader = new FileReader();

        Reader.readAsDataURL(file);

        Reader.onload = () => {
            if (Reader.readyState === 2) {
                setAvatar(Reader.result);
            }
        };
    }

    const handleUpdateAvatar = (data) => {

        const payload = {
            avatar: avatar,
        }

        axios
            .put(`http://localhost:8000/api/user/avatar/upload`, payload, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then(function (response) {
                if (response.data.success) {
                    setSuccess(response.data.success)
                    setMessage(response.data.message)
                } else {
                    setSuccess(response.data.success)
                    setMessage(response.data.message);
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    return (
        <section id={styles.accountEdit} className='ptb100'>
            <Container fluid>
                <Row>
                    <Col lg={6}>
                        <div className={styles.backBtn}>
                            <Link to='/my-account/customer-account-details'><FaArrowLeft /> Trở về trang Dashboard</Link>
                        </div>
                    </Col>
                </Row>
                <Row>
                    <Col lg={3}>
                        <div className={styles.accountThumd}>
                            <form onSubmit={handleSubmit3(handleUpdateAvatar)}>
                                <div className={styles.accountThumbImg}>
                                    <img src={avatar} alt="img" />
                                    <div className={styles.fixedIcon}>
                                        <input
                                            className="FormInput"
                                            type="file"
                                            accept='image/*'
                                            {...register3("avatar", { onChange: handleImage })}
                                        /><FaCamera />
                                    </div>
                                </div>
                                <div className='m-4 mx-auto'>
                                    <AccountEditModal nameBtn='Cập nhật ảnh đại diện' message={message} success={success} />
                                </div>
                            </form>
                        </div>
                    </Col>
                    <Col lg={9}>
                        <div className={styles.accountSetting}>
                            <div className={styles.accountSettingHeading}>
                                <h2>Thông tin tài khoản</h2>
                                <p>Chỉnh sửa thông tin tài khoản của bạn và thay đổi mật khẩu của bạn tại đây.</p>
                            </div>
                            <form onSubmit={handleSubmit(handleUpdateInformation)} id='accountEditFormInformation' className={styles.accountEditForm}>
                                <div className={styles.formGroup}>
                                    <label htmlFor="firstName">Họ</label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="VD: Lê Quốc"
                                        {...register("firstName", { required: true, minLength: 2, maxLength: 50, onChange: (e) => { setFirstName(e.target.value) } })}
                                    />
                                    {errors["firstName"] && (
                                        <p className="checkInput">Họ không hợp lệ</p>
                                    )}
                                    <label htmlFor="lastName">Tên</label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="VD: Bảo"
                                        {...register("lastName", { required: true, minLength: 2, maxLength: 50, onChange: (e) => { setLastName(e.target.value) } })}
                                    />
                                    {errors["lastName"] && (
                                        <p className="checkInput">Tên không hợp lệ</p>
                                    )}
                                </div>

                                <div className={styles.formGroup}>
                                    <label htmlFor="email">Email</label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="Username or Email"
                                        {...register("email", { required: true, pattern: /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/, onChange: (e) => { setEmail(e.target.value) } })}
                                    />
                                    {errors["email"] && (
                                        <p className="checkInput">Email không hợp lệ</p>
                                    )}
                                </div>
                                <AccountEditModal nameBtn='Cập nhật thông tin' message={message} success={success} />
                            </form>
                            <form onSubmit={handleSubmit2(handleUpdatePassword)} id='accountEditFormPassword' className={styles.accountEditForm}>
                                <div className={styles.formGroup}>
                                    <label htmlFor="password">Mật khẩu hiện tại
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="password"
                                        placeholder="Mật khẩu hiện tại"
                                        {...register2("password", { required: true, minLength: 3, maxLength: 24 })}
                                    />
                                    {errors2["password"] && (
                                        <p className="checkInput">Mật khẩu phải có từ 3 đến 24 ký tự</p>
                                    )}
                                    <label htmlFor="newPassword">Mật khẩu mới</label>
                                    <input
                                        className="FormInput"
                                        type="password"
                                        placeholder="Mật khẩu mới"
                                        {...register2("newPassword", { required: true, minLength: 6, maxLength: 24 })}
                                    />
                                    {errors2["newPassword"] && (
                                        <p className="checkInput">Mật khẩu mới phải có từ 6 đến 24 ký tự</p>
                                    )}
                                    <label htmlFor="confirmNewPassword">Xác nhận mật khẩu mới</label>
                                    <input
                                        className="FormInput"
                                        type="password"
                                        placeholder="Xác nhận mật khẩu mới"
                                        {...register2("confirmNewPassword", { required: true, minLength: 6, maxLength: 24 })}
                                    />
                                    {errors2["confirmNewPassword"] && (
                                        <p className="checkInput">Mật khẩu hiện tại và mật khẩu mới phải giống nhau</p>
                                    )}
                                </div>
                                <AccountEditModal nameBtn='Cập nhật mật khẩu' message={message} success={success} />
                            </form>
                        </div>
                    </Col>
                </Row>
            </Container >
        </section >
    )
}

export default AccountEditArea;