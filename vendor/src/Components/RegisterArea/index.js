import styles from './RegisterArea.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { useForm, Controller } from "react-hook-form";
import axios from '../../service/axiosClient';
import PhoneInput, { isValidPhoneNumber } from "react-phone-number-input";
import 'react-phone-number-input/style.css';
import './PhoneInput.css';

function RegisterArea() {
    const {
        register,
        handleSubmit,
        formState: { errors },
        control,
    } = useForm();

    const handleValidate = (value) => {
        const isValid = isValidPhoneNumber(value);
        return isValid
    }

    const onSubmit = (data) => {
        console.log(data);
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
                                        {...register("firstName", { required: true, minLength: 3 })}
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
                                        {...register("lastName", { required: true, minLength: 3 })}
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
                                        {...register("email", { required: true, pattern: /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i })}
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
                                        {...register("password", { required: true, minLength: 3 })}
                                    />
                                    {errors["password"] && (
                                        <p className="checkInput">Password must be at 3 char long</p>
                                    )}
                                </div>
                                <div className={styles.defaultFormBox}>
                                    <Controller
                                        name="phoneNumber"
                                        control={control}
                                        rules={{
                                            validate: (value) => handleValidate(value)
                                        }}
                                        render={({ field: { onChange, value } }) => (
                                            <PhoneInput
                                                value={value}
                                                onChange={onChange}
                                                defaultCountry="VN"
                                                id="phoneNumber"
                                                placeholder="Phone"
                                                required
                                            />
                                        )}
                                    />
                                    {errors["phoneNumber"] && (
                                        <p className="checkInput">Invalid Phone!</p>
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