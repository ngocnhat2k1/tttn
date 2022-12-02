import styles from "../AddressEditArea/AddressEditArea.module.css"
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { useState, useEffect } from 'react';
import { useForm, Controller } from "react-hook-form";
import PhoneInput, { isValidPhoneNumber } from "react-phone-number-input";
import 'react-phone-number-input/style.css';
import '../RegisterArea/PhoneInput.css';
import Cookies from 'js-cookie';
import axios from 'axios';
import { Link } from 'react-router-dom';
import { FaArrowLeft } from 'react-icons/fa';
import { FaRegCheckCircle, FaTimesCircle } from 'react-icons/fa'

function CreateAddressArea() {

    const [phoneNumber, setPhoneNumber] = useState('');
    const [message, setMessage] = useState('');
    const [success, setSuccess] = useState('');
    const [modal, setModal] = useState(false);
    const { register, handleSubmit, watch, formState: { errors }, control, reset } = useForm();

    const closeModal = () => {
        setModal(!modal);
    }

    const onSubmit = (data) => {
        let { id, customerId, ...rest } = data;

        axios
            .post(`http://localhost:8000/api/user/address/create`, rest,
                {
                    headers: {
                        Authorization: `Bearer ${Cookies.get('token')}`,
                    }
                },
            )
            .then(response => {
                console.log(response)
                if (response.data.success) {
                    setMessage(response.data.message)
                    setSuccess(response.data.success)
                    setModal(!modal)
                } else {
                    setMessage(response.data.errors)
                    setSuccess(response.data.success)
                    setModal(!modal)
                }
            })
            .catch(err => {
                console.log(err);
            })
    }

    return (
        <section id={styles.addressEdit} className='ptb100'>
            <Container>
                <Row>
                    <Col lg={6}>
                        <div className={styles.backBtn}>
                            <Link to='/my-account/customer-address'><FaArrowLeft /> Trở về trang cá nhân</Link>
                        </div>
                    </Col>
                </Row>
                <Row>
                    <Col lg={{ span: 6, offset: 3 }} md={12} sm={12} xs={12}>
                        <div className={styles.addressForm}>
                            <h2>Thêm địa chỉ giao hàng mới</h2>
                            <form onSubmit={handleSubmit(onSubmit)}>
                                <div className={styles.formGroup}>
                                    <label htmlFor="firstNameReceiver">
                                        Họ người nhận
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="VD: Lê Quốc"
                                        {...register("firstNameReceiver", { required: true, minLength: 2, maxLength: 100 })} />
                                    {errors.firstNameReceiver && errors.firstNameReceiver.type === "required" && (
                                        <p className="checkInput">Họ không được để trống</p>
                                    )}
                                    {errors.firstNameReceiver && errors.firstNameReceiver.type === "minLength" && (
                                        <p className="checkInput">Họ phải có ít nhất 2 ký tự</p>
                                    )}
                                    {errors.firstNameReceiver && errors.firstNameReceiver.type === "maxLength" && (
                                        <p className="checkInput">Họ chỉ được nhiều nhất 100 ký tự</p>
                                    )}
                                    <label htmlFor="lastNameReceiver">
                                        Tên người nhận
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="VD: Bảo"
                                        {...register("lastNameReceiver", { required: true, minLength: 2, maxLength: 100 })} />
                                    {errors.lastNameReceiver && errors.lastNameReceiver.type === "required" && (
                                        <p className="checkInput">Tên không được để trống</p>
                                    )}
                                    {errors.lastNameReceiver && errors.lastNameReceiver.type === "minLength" && (
                                        <p className="checkInput">Tên phải có ít nhất 2 ký tự</p>
                                    )}
                                    {errors.lastNameReceiver && errors.lastNameReceiver.type === "maxLength" && (
                                        <p className="checkInput">Tên chỉ được nhiều nhất 100 ký tự</p>
                                    )}
                                    <label htmlFor="streetName">
                                        Tên đường
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="VD: Tô Ký"
                                        {...register("streetName", { required: true, minLength: 2 })} />
                                    {errors.streetName && errors.streetName.type === "required" && (
                                        <p className="checkInput">Tên đường không được để trống</p>
                                    )}
                                    {errors.streetName && errors.streetName.type === "minLength" && (
                                        <p className="checkInput">Tên đường phải có ít nhất 2 ký tự</p>
                                    )}
                                    <label htmlFor="district">Quận / Huyện</label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="VD: 12"
                                        {...register("district", { required: true })} />
                                    {errors.district && errors.district.type === "required" && (
                                        <p className="checkInput">Quận / Huyện không được để trống</p>
                                    )}
                                    <label htmlFor="ward">
                                        Phường / Xã
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="VD: Tân Chánh Hiệp"
                                        {...register("ward", { required: true })} />
                                    {errors.ward && errors.ward.type === "required" && (
                                        <p className="checkInput">Phường / Xã không được để trống</p>
                                    )}
                                    <label htmlFor="city">
                                        Tỉnh / Thành phố
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        placeholder="VD: Hồ Chí Minh"
                                        {...register("city", { required: true })} />
                                    {errors.city && errors.city.type === "required" && (
                                        <p className="checkInput">Phường / Xã không được để trống</p>
                                    )}
                                    <Controller
                                        name="phoneReceiver"
                                        control={control}
                                        rules={{
                                            validate: (value) => isValidPhoneNumber(value.toString())
                                        }}
                                        render={({ field: { onChange, value } }) => (
                                            <PhoneInput
                                                value={phoneNumber.toString()}
                                                onChange={onChange}
                                                defaultCountry="VN"
                                                placeholder="VD: 0969710601"
                                                id="phoneReceiver" />)} />
                                    {errors["phoneReceiver"] && (
                                        <p className="checkInput">Số điện thoại không đúng định dạng</p>
                                    )}
                                    <button type='submit' className='theme-btn-one bg-black btn_sm'>Thêm địa chỉ giao hàng</button>

                                    {modal && (
                                        <div className="modal">
                                            <div onClick={closeModal} className="overlay"></div>
                                            <div className="modal-content">
                                                <div>
                                                    {success === true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                                                </div>
                                                <h2 className="title_modal">Thêm địa chỉ {success ? 'thành công' : 'thất bại'}</h2>
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

export default CreateAddressArea;
