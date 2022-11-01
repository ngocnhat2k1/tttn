import styles from './LoginArea.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { useForm } from "react-hook-form";
import { Link } from 'react-router-dom';
import axios from '../../service/axiosClient';
import Cookies from 'js-cookie';
import '../RegisterArea/PhoneInput.css'

function LoginArea() {


    const {
        register,
        handleSubmit,
        formState: { errors },
    } = useForm();


    const onSubmit = (data) => {
        axios
            .post('http://localhost:8000/api/admin/login', data)
            .then(function (response) {
                if (response.data.success) {
                    const token = response.data.token;
                    Cookies.set('token', token, { path: '/' });
                    window.location.href = 'http://localhost:3000';
                } else {
                    alert('Sai tài khoản hoặc mật khẩu!');
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    };

    return (
        <section id={styles.loginArea}>
            <Container>
                <Row>
                    <Col lg={{ span: 6, offset: 3 }} md={12} sm={12} xs={12}>
                        <div className={styles.accountForm}>
                            <h3>Login</h3>
                            <form onSubmit={handleSubmit(onSubmit)}>
                                <div className={styles.defaultFormBox}>
                                    <label htmlFor="email">Email
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="Username or Email"
                                        {...register("email", { required: true, minLength: 3 })}
                                    />
                                    {errors["email"] && (
                                        <p className="checkInput">Invalid Username or Email!</p>
                                    )}
                                </div>
                                <div className={styles.defaultFormBox}>
                                    <label htmlFor="password">Password
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="password"
                                        placeholder="Password"
                                        {...register("password", { required: true, min: 3 })}
                                    />
                                    {errors["password"] && (
                                        <p className="checkInput">Password must be at 3 char long</p>
                                    )}
                                </div>
                                <div className={styles.loginSubmit}>
                                    <button type="submit">LOGIN</button>
                                </div>
                                <Link to="/register" className={styles.createAccount}>Create Your Account?</Link>
                            </form>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default LoginArea