import styles from './RegisterArea.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { useForm, Controller } from "react-hook-form";
import axios from '../../service/axiosClient';

function RegisterArea() {
    const {
        register,
        handleSubmit,
        formState: { errors },
        control,
    } = useForm();

    const onSubmit = (data) => {
        axios
            .post('http://localhost:8000/api/register', data)
            .then(function (response) {
                console.log("1", response.data.success)
                if (response.data.success) {
                    alert('Success')
                } else {
                    console.log("2", response.data.errors)
                    alert('Fail!');
                }
            })
            .catch(function (error) {
                console.log(error);
            });
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
                                    <label htmlFor="firstName">First Name
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="First name"
                                        {...register("firstName", { required: true, minLength: 2 })}
                                    />
                                    {errors["firstName"] && (
                                        <p className="checkInput">Invalid First Name!</p>
                                    )}
                                </div>
                                <div className={styles.defaultFormBox}>
                                    <label htmlFor="lastName">Last Name
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="Last name"
                                        {...register("lastName", { required: true, minLength: 2 })}
                                    />
                                    {errors["lastName"] && (
                                        <p className="checkInput">Invalid Last Name!</p>
                                    )}
                                </div>
                                <div className={styles.defaultFormBox}>
                                    <label htmlFor="email">Email
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="Email"
                                        {...register("email", { required: true, pattern: /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/i })}
                                    />
                                    {errors["email"] && (
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
                                        {...register("password", { required: true, minLength: 6, maxLength: 24 })}
                                    />
                                    {errors["password"] && (
                                        <p className="checkInput">Password must be at least 6 characters and max 24 characters long</p>
                                    )}
                                </div>
                                <div className={styles.defaultFormBox}>
                                    <label htmlFor="confirmPassword">Confirm Password
                                        <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="password"
                                        placeholder="Confirm Password"
                                        {...register("confirmPassword", { required: true, minLength: 6, maxLength: 24 })}
                                    />
                                    {errors["confirmPassword"] && (
                                        <p className="checkInput">Those passwords didn't match. Try again.</p>
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