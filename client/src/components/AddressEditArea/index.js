import styles from './AddressEditArea.module.css';
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
import { Link, useParams } from 'react-router-dom';
import { FaArrowLeft } from 'react-icons/fa';
import { FaRegCheckCircle, FaTimesCircle } from 'react-icons/fa'

function AddressEditArea() {

    const { id } = useParams();
    const [phoneNumber, setPhoneNumber] = useState('');
    const [message, setMessage] = useState('');
    const [success, setSuccess] = useState('');
    const [modal, setModal] = useState(false);
    const { register, handleSubmit, watch, formState: { errors }, control, reset } = useForm();

    useEffect(() => {
        axios
            .get(`http://localhost:8000/api/user/address/${id}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then(response => {
                if (response.data.success) {
                    reset(response.data.data);
                    setPhoneNumber(response.data.data.phoneReceiver)
                }
            })
            .catch(error => {
                console.log(error);
            });
    }, []);

    const closeModal = () => {
        setModal(!modal);
    }

    const onSubmit = (data) => {
        let { id, customerId, ...rest } = data;

        axios
            .put(`http://localhost:8000/api/user/address/update/${id}`, rest,
                {
                    headers: {
                        Authorization: `Bearer ${Cookies.get('token')}`,
                    }
                },
            )
            .then(response => {
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

    if (modal) {
        document.body.classList.add('active-modal')
    } else {
        document.body.classList.remove('active-modal')
    }

    return (
        <section id={styles.addressEdit} className='ptb100'>
            <Container>
                <Row>
                    <Col lg={6}>
                        <div className={styles.backBtn}>
                            <Link to='/my-account/customer-address'><FaArrowLeft /> Tr??? v??? trang c?? nh??n</Link>
                        </div>
                    </Col>
                </Row>
                <Row>
                    <Col lg={{ span: 6, offset: 3 }} md={12} sm={12} xs={12}>
                        <div className={styles.addressForm}>
                            <h2>?????a ch??? giao h??ng</h2>
                            <form onSubmit={handleSubmit(onSubmit)}>
                                <div className={styles.formGroup}>
                                    <label htmlFor="firstNameReceiver">
                                        H??? ng?????i nh???n
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        {...register("firstNameReceiver", { required: true, minLength: 2, maxLength: 100 })} />
                                    {errors.firstNameReceiver && errors.firstNameReceiver.type === "required" && (
                                        <p className="checkInput">H??? kh??ng ???????c ????? tr???ng</p>
                                    )}
                                    {errors.firstNameReceiver && errors.firstNameReceiver.type === "minLength" && (
                                        <p className="checkInput">H??? ph???i c?? ??t nh???t 2 k?? t???</p>
                                    )}
                                    {errors.firstNameReceiver && errors.firstNameReceiver.type === "maxLength" && (
                                        <p className="checkInput">H??? ch??? ???????c nhi???u nh???t 100 k?? t???</p>
                                    )}
                                    <label htmlFor="lastNameReceiver">
                                        T??n ng?????i nh???n
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        {...register("lastNameReceiver", { required: true, minLength: 2, maxLength: 100 })} />
                                    {errors.lastNameReceiver && errors.lastNameReceiver.type === "required" && (
                                        <p className="checkInput">T??n kh??ng ???????c ????? tr???ng</p>
                                    )}
                                    {errors.lastNameReceiver && errors.lastNameReceiver.type === "minLength" && (
                                        <p className="checkInput">T??n ph???i c?? ??t nh???t 2 k?? t???</p>
                                    )}
                                    {errors.lastNameReceiver && errors.lastNameReceiver.type === "maxLength" && (
                                        <p className="checkInput">T??n ch??? ???????c nhi???u nh???t 100 k?? t???</p>
                                    )}
                                    <label htmlFor="streetName">
                                        T??n ???????ng
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        {...register("streetName", { required: true, minLength: 2 })} />
                                    {errors.streetName && errors.streetName.type === "required" && (
                                        <p className="checkInput">T??n ???????ng kh??ng ???????c ????? tr???ng</p>
                                    )}
                                    {errors.streetName && errors.streetName.type === "minLength" && (
                                        <p className="checkInput">T??n ???????ng ph???i c?? ??t nh???t 2 k?? t???</p>
                                    )}
                                    <label htmlFor="district">Qu???n / Huy???n</label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        {...register("district", { required: true })} />
                                    {errors.district && errors.district.type === "required" && (
                                        <p className="checkInput">Qu???n / Huy???n kh??ng ???????c ????? tr???ng</p>
                                    )}
                                    <label htmlFor="ward">
                                        Ph?????ng / X??
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        {...register("ward", { required: true })} />
                                    {errors.ward && errors.ward.type === "required" && (
                                        <p className="checkInput">Ph?????ng / X?? kh??ng ???????c ????? tr???ng</p>
                                    )}
                                    <label htmlFor="city">
                                        T???nh / Th??nh ph???
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        {...register("city", { required: true })} />
                                    {errors.city && errors.city.type === "required" && (
                                        <p className="checkInput">Ph?????ng / X?? kh??ng ???????c ????? tr???ng</p>
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
                                                id="phoneReceiver" />)} />
                                    {errors["phoneReceiver"] && (
                                        <p className="checkInput">S??? ??i???n tho???i kh??ng ????ng ?????nh d???ng</p>
                                    )}
                                    <button type='submit' className='theme-btn-one bg-black btn_sm'>C???p nh???t ?????a ch??? giao h??ng</button>

                                    {modal && (
                                        <div className="modal">
                                            <div onClick={closeModal} className="overlay"></div>
                                            <div className="modal-content">
                                                <div>
                                                    {success === true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                                                </div>
                                                <h2 className="title_modal">C???p nh???t {success ? 'th??nh c??ng' : 'th???t b???i'}</h2>
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

export default AddressEditArea;