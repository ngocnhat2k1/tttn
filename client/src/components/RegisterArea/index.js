import styles from './RegisterArea.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { useForm } from "react-hook-form";
import axios from '../../service/axiosClient';
import { FaRegCheckCircle, FaTimesCircle } from 'react-icons/fa'
import { useState } from 'react'
import "../ModalATag/Modal.css"

function RegisterArea() {
    const [message, setMessage] = useState('');
    const [success, setSuccess] = useState('');
    const [modal, setModal] = useState(false);

    const {
        register,
        handleSubmit,
        formState: { errors }
    } = useForm();

    const closeModal = () => {
        if (success) {
            setModal(!modal);
            window.location.href = 'http://localhost:3000/login';
        } else {
            setModal(!modal);
        }
    }

    const onSubmit = (data) => {
        axios
            .post('http://localhost:8000/api/register', data)
            .then(response => {
                console.log(response.data)
                if (response.data.success) {
                    setMessage(response.data.message);
                    setSuccess(response.data.success);
                    setModal(!modal);
                } else {
                    setMessage(response.data.errors);
                    setSuccess(response.data.success);
                    setModal(!modal);
                }
            })
            .catch(error => {
                console.log(error);
            });
    };

    return (
        <section id={styles.registerArea}>
            <Container>
                <Row>
                    <Col lg={{ span: 6, offset: 3 }} md={12} sm={12} xs={12}>
                        <div className={styles.accountForm}>
                            <h3>Đăng ký</h3>
                            <form onSubmit={handleSubmit(onSubmit)}>
                                <div className={styles.defaultFormBox}>
                                    <label htmlFor="firstName">Họ
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="VD: Lê Quốc"
                                        {...register("firstName", { required: true, minLength: 2, maxLength: 50 })}
                                    />
                                    {errors.firstName && errors.firstName.type === "required" && (
                                        <p className="checkInput">Họ không được để trống</p>
                                    )}
                                    {errors.firstName && errors.firstName.type === "minLength" && (
                                        <p className="checkInput">Họ phải có ít nhất 2 ký tự</p>
                                    )}
                                    {errors.firstName && errors.firstName.type === "maxLength" && (
                                        <p className="checkInput">Họ chỉ được tối đa 50 ký tự</p>
                                    )}
                                </div>
                                <div className={styles.defaultFormBox}>
                                    <label htmlFor="lastName">Tên
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="VD: Bảo"
                                        {...register("lastName", { required: true, minLength: 2, maxLength: 50 })}
                                    />
                                    {errors.lastName && errors.lastName.type === "required" && (
                                        <p className="checkInput">Tên không được để trống</p>
                                    )}
                                    {errors.lastName && errors.lastName.type === "minLength" && (
                                        <p className="checkInput">Tên phải có ít nhất 2 ký tự</p>
                                    )}
                                    {errors.lastName && errors.lastName.type === "maxLength" && (
                                        <p className="checkInput">Tên chỉ được tối đa 50 ký tự</p>
                                    )}
                                </div>
                                <div className={styles.defaultFormBox}>
                                    <label htmlFor="email">Email
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="VD: lqbao240101@gmail.com"
                                        {...register("email", { required: true, pattern: /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/i })}
                                    />
                                    {errors.email && errors.email.type === "required" && (
                                        <p className="checkInput">Email không được để trống</p>
                                    )}
                                    {errors.email && errors.email.type === "pattern" && (
                                        <p className="checkInput">Email không hợp lệ</p>
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
                                        {...register("password", { required: true, minLength: 6, maxLength: 24 })}
                                    />
                                    {errors.password && errors.password.type === "required" && (
                                        <p className="checkInput">Mật khẩu không được để trống</p>
                                    )}
                                    {errors.password && errors.password.type === "minLength" && (
                                        <p className="checkInput">Mật khẩu phải có ít nhất 6 ký tự</p>
                                    )}
                                    {errors.password && errors.password.type === "maxLength" && (
                                        <p className="checkInput">Mật khẩu chỉ được tối đa 24 ký tự</p>
                                    )}
                                </div>
                                <div className={styles.defaultFormBox}>
                                    <label htmlFor="confirmPassword">Xác nhận mật khẩu
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="password"
                                        placeholder="Xác nhận mật khẩu"
                                        {...register("confirmPassword", { required: true, minLength: 6, maxLength: 24 })}
                                    />
                                    {errors.confirmPassword && errors.confirmPassword.type === "required" && (
                                        <p className="checkInput">Xác nhận mật khẩu không được để trống</p>
                                    )}
                                    {errors.confirmPassword && errors.confirmPassword.type === "minLength" && (
                                        <p className="checkInput">Xác nhận mật khẩu phải có ít nhất 6 ký tự</p>
                                    )}
                                    {errors.confirmPassword && errors.confirmPassword.type === "maxLength" && (
                                        <p className="checkInput">Xác nhận mật khẩu chỉ được tối đa 24 ký tự</p>
                                    )}
                                </div>
                                <div className={styles.registerSubmit}>
                                    <button type="submit">ĐĂNG KÝ</button>

                                    {modal && (
                                        <div className="modal">
                                            <div onClick={closeModal} className="overlay"></div>
                                            <div className="modal-content">
                                                <div>
                                                    {success === true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                                                </div>
                                                <h2 className="title_modal">Đăng ký {success ? 'thành công' : 'thất bại'}</h2>
                                                <p className='p_modal'>{message}</p>
                                                <div className='divClose'>
                                                    <button className="close close-modal" onClick={closeModal}>OK</button>
                                                </div>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </form>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default RegisterArea