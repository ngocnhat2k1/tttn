import styles from './ContactForm.module.scss'
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row';
import { useForm, Controller } from "react-hook-form";
import PhoneInput, { isValidPhoneNumber } from "react-phone-number-input";
import 'react-phone-number-input/style.css'
import './PhoneInput.css'

function ContactForm() {

    const {
        register,
        handleSubmit,
        formState: { errors },
        control
    } = useForm();

    const handleValidate = (value) => {
        const isValid = isValidPhoneNumber(value);
        return isValid
    }

    const onSubmit = (data) => {
        console.log({ data })

    };

    return (
        <Col lg={8}>
            <div className={styles.contactForm}>
                <h3>Liên hệ</h3>
                <form onSubmit={handleSubmit(onSubmit)}>
                    <Row>
                        <Col lg={6} md={6} sm={6} xs={12}>
                            <div className={styles.formGroup}>
                                <input
                                    className="FormInput"
                                    type="text"
                                    placeholder="Họ và Tên"
                                    {...register("name-input", { required: true, pattern: /[^a-z0-9A-Z_ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễếệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ]/u })}
                                />
                                {errors["name-input"] && (
                                    <p className="checkInput">Tên không hợp lệ</p>
                                )}
                            </div>
                        </Col>
                        <Col lg={6} md={6} sm={6} xs={12}>
                            <div className={styles.formGroup}>
                                <input
                                    className="FormInput"
                                    type="text"
                                    placeholder="Email"
                                    {...register("email-input", { required: true, pattern: /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i })}
                                />
                                {errors["email-input"] && (
                                    <p className="checkInput">Email không hợp lệ</p>
                                )}
                            </div>
                        </Col>
                        <Col lg={6} md={6} sm={6} xs={12}>
                            <div className={styles.formGroup}>
                                <Controller
                                    name="phone-input"
                                    control={control}
                                    rules={{
                                        validate: (value) => handleValidate(value)
                                    }}
                                    render={({ field: { onChange, value } }) => (
                                        <PhoneInput
                                            value={value}
                                            onChange={onChange}
                                            defaultCountry="VN"
                                            id="phone-input"
                                            placeholder="Sô điện thoại"
                                            required
                                        />
                                    )}
                                />
                                {errors["phone-input"] && (
                                    <p className="checkInput">Số điện thoại không hợp lệ</p>
                                )}
                            </div>
                        </Col>
                        <Col lg={6} md={6} sm={6} xs={12}>
                            <div className={styles.formGroup}>
                                <input
                                    className="FormInput"
                                    type="text"
                                    placeholder="Vấn đề của bạn"
                                    {...register("subject-input", { required: true })}
                                />
                                {errors["subject-input"] && (
                                    <p className="checkInput">Hãy điền vấn đề mà bạn gặp phải</p>
                                )}
                            </div>
                        </Col>
                        <Col lg={12} md={12} sm={12} xs={12}>
                            <div className={styles.formGroup}>
                                <textarea
                                    className="FormInput"
                                    type="text"
                                    placeholder="Nội dung"
                                    {...register("message-input", { required: true })}
                                    rows={7}
                                >
                                </textarea>
                                {errors["subject-input"] && (
                                    <p className="checkInput">Nội dung trống!</p>
                                )}
                            </div>
                            <div className={styles.submitButtonContact}>
                                <button type="Submit">GỬI EMAIL</button>
                            </div>
                        </Col>
                    </Row>
                </form>
            </div>
        </Col>
    )
}

export default ContactForm