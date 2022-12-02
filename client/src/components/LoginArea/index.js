import styles from './LoginArea.module.css';
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { useForm } from "react-hook-form";
import { Link } from 'react-router-dom';
import axios from '../../service/axiosClient';
import Cookies from 'js-cookie';
import { FaRegCheckCircle, FaTimesCircle } from 'react-icons/fa'
import { useState } from 'react'

function LoginArea() {

    const [message, setMessage] = useState('');
    const [success, setSuccess] = useState('');
    const [modal, setModal] = useState(false);

    const {
        register,
        handleSubmit,
        formState: { errors },
    } = useForm();

    const closeModal = () => {
        if (success) {
            setModal(!modal);
            window.location.href = 'http://localhost:3000/my-account';
        } else {
            setModal(!modal);
        }
    }

    const onSubmit = (data) => {
        console.log(data);
        axios
            .post(`http://localhost:8000/api/login`, data)
            .then(response => {
                console.log(response)
                if (response.data.success) {
                    const token = response.data.token;
                    Cookies.set('token', token, { path: '/' });
                    setSuccess(response.data.success);
                    setModal(!modal)
                } else {
                    setMessage(response.data.errors);
                    setSuccess(response.data.success);
                    setModal(!modal)
                }
            })
            .catch(error => {
                console.log(error);
            });
    };

    return (
        <section id={styles.loginArea}>
            <Container>
                <Row>
                    <Col lg={{ span: 6, offset: 3 }} md={12} sm={12} xs={12}>
                        <div className={styles.accountForm}>
                            <h3>Đăng nhập</h3>
                            <form onSubmit={handleSubmit(onSubmit)}>
                                <div className={styles.defaultFormBox}>
                                    <label htmlFor="email">Email
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="Email"
                                        {...register("email", { required: true, minLength: 3 })}
                                    />
                                    {errors.email && errors.email.type === "required" && (
                                        <p className="checkInput">Email không được để trống</p>
                                    )}
                                </div>
                                <div className={styles.defaultFormBox}>
                                    <label htmlFor="password">Mật khẩu
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="password"
                                        placeholder="Mật khẩu"
                                        {...register("password", { required: true, minLength: 3, maxLength: 24 })}
                                    />
                                    {errors.password && errors.password.type === "required" && (
                                        <p className="checkInput">Mật khẩu không được để trống</p>
                                    )}
                                    {errors.password && errors.password.type === "minLength" && (
                                        <p className="checkInput">Mật khẩu phải có ít nhất 3 ký tự</p>
                                    )}
                                    {errors.password && errors.password.type === "maxLength" && (
                                        <p className="checkInput">Mật khẩu chỉ được tối đa 24 ký tự</p>
                                    )}
                                </div>
                                <div className={styles.loginSubmit}>
                                    <button type="submit">Đăng nhập</button>

                                    {modal && (
                                        <div className="modal">
                                            <div onClick={closeModal} className="overlay"></div>
                                            <div className="modal-content">
                                                <div>
                                                    {success === true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                                                </div>
                                                <h2 className="title_modal">Đăng nhập {success ? 'thành công' : 'thất bại'}</h2>
                                                <p className='p_modal'>{message}</p>
                                                <div className='divClose'>
                                                    <button className="close close-modal" onClick={closeModal}>OK</button>
                                                </div>

                                            </div>
                                        </div>
                                    )}

                                </div>
                                <Link to="/register" className={styles.createAccount}>Tạo tài khoản mới?</Link>
                            </form>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default LoginArea