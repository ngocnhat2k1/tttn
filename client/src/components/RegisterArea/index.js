import styles from './RegisterArea.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { useForm } from "react-hook-form";

function RegisterArea() {
    const {
        register,
        handleSubmit,
        formState: { errors },
    } = useForm();

    const onSubmit = (data) => {
        console.log({ data })
    };

    return (
        <section id={styles.registerArea}>
            <Container>
                <Row>
                    <Col lg={{ span: 6, offset: 3 }} md={12} sm={12} xs={12}>
                        <div className={styles.accountForm}>
                            <h3>Register</h3>
                            <form onSubmit={handleSubmit(onSubmit)}>
                                <div className={styles.defaultFormBox}>
                                    <label htmlFor="isername-input">Username
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="Username"
                                        {...register("username-input", { required: true, minLength: 3 })}
                                    />
                                    {errors["username-input"] && (
                                        <p className="checkInput">Invalid Username!</p>
                                    )}
                                </div>
                                <div className={styles.defaultFormBox}>
                                    <label htmlFor="email-input">Email
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="Email"
                                        {...register("email-input", { required: true, pattern: /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i  })}
                                    />
                                    {errors["email-input"] && (
                                        <p className="checkInput">Invalid Email!</p>
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
                                        {...register("password", { required: true, minLength: 3 })}
                                    />
                                    {errors["password"] && (
                                        <p className="checkInput">Password must be at 3 char long</p>
                                    )}
                                </div>
                                <div className={styles.registerSubmit}>
                                    <button type="submit">REGISTER</button>
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