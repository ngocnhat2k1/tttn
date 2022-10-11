import styles from './LoginArea.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { useForm } from "react-hook-form";
import { Link } from 'react-router-dom';
import { useCookies } from 'react-cookie';
import axios from '../../service/axiosClient';

function LoginArea() {
    const [cookies, setCookie, removeCookie] = useCookies([]);

    const {
        register,
        handleSubmit,
        formState: { errors },
    } = useForm();


    const onSubmit = (data) => {
        console.log({ data })
        setCookie('token', 'hello', { path: '/' });
        console.log(cookies);
        // axios
        //     .post('http://localhost:8080/tttn_be/public/api/user/login', { data })
        //     .then(function (response) {
        //         console.log(response.data.result);
        //         if (response.data.result) {
        //             const accessToken = response.data.access_token;

        //             setCookie('token', 'hello', { path: '/' });

        //             setAuth({ data.account, data.password, roles, accessToken });
        //         } else {
        //             alert(response.data.message);
        //         }
        //     })
        //     .catch(function (error) {
        //         console.log(error);
        //     });
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
                                    <label htmlFor="account">Username or email
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="Username or Email"
                                        {...register("account", { required: true, minLength: 3 })}
                                    />
                                    {errors["account"] && (
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