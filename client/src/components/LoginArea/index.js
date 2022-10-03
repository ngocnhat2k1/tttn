import styles from './LoginArea.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { useForm } from "react-hook-form";
import { Link } from 'react-router-dom';

function LoginArea() {

    const {
        register,
        handleSubmit,
        formState: { errors },
    } = useForm();

    const onSubmit = (data) => {
        console.log({ data })
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
                                    <label htmlFor="account-input">Username or email
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="Username or Email"
                                        {...register("account-input", { required: true, minLength: 3})}
                                    />
                                    {errors["account-input"] && (
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
                                        {...register("password", { required: true, min:3 })}
                                    />
                                    {errors["password"] && (
                                        <p className="checkInput">Password must be at 3 char long</p>
                                    )}
                                </div>
                                <div className={styles.loginSubmit}>
                                    <button type="submit">LOGIN</button>
                                </div>
                                <div className={styles.rememberArea}>
                                    <div className="form-check">
                                        <input type="checkbox" className="form-check-input" id="rememberinput"/>
                                        <label className="form-check-label" htmlFor="rememberinput">Remember me</label>
                                    </div>
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