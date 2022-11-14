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
import { Link } from 'react-router-dom';
import { FaArrowLeft } from 'react-icons/fa';

function AddressEditArea({ id, stt }) {
    console.log(stt);

    const [lastName, setLastName] = useState('');
    const [firstName, setFirstName] = useState('');
    const [streetName, setStreetName] = useState('');
    const [district, setDistrict] = useState('');
    const [ward, setWard] = useState('');
    const [city, setCity] = useState('');
    const [phoneNumber, setPhoneNumber] = useState('');
    const [errors, setError] = useState({
        firstName: false,
        lastName: false,
        streetName: false,
        district: false,
        ward: false,
        city: false,
        phoneNumber: false
    });

    useEffect(() => {
        axios
            .get(`http://localhost:8000/api/user/address/${id}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                console.log(response)
                if (response.data.success) {
                    setLastName(response.data.data.lastName)
                    setFirstName(response.data.data.firstName)
                    setStreetName(response.data.data.streetName)
                    setDistrict(response.data.data.district)
                    setWard(response.data.data.ward)
                    setCity(response.data.data.city)
                    setPhoneNumber(response.data.data.phoneReceiver)
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }, [id]);

    const {
        control
    } = useForm();

    const handleValidate = (value) => {
        const isValid = isValidPhoneNumber(value);
        return isValid
    }

    const handleUpdateName = () => {
        if (firstName === '' || firstName.length < 2 || firstName.length > 50) {
            setError((prevState) => ({
                ...prevState,
                firstName: true,
            }));
        } else {
            setError((prevState) => ({
                ...prevState,
                firstName: false,
            }));
        }

        if (lastName === '' || lastName.length < 2 || firstName.length > 50) {
            setError((prevState) => ({
                ...prevState,
                lastName: true,
            }));
        } else {
            setError((prevState) => ({
                ...prevState,
                lastName: false,
            }));
        }
    }

    return (
        <section id={styles.addressEdit} className='ptb100'>
            <Container>
                <Row>
                    <Col lg={6}>
                        <div className={styles.backBtn}>
                            <Link to='/my-account/customer-address'><FaArrowLeft /> Back to Dashboard</Link>
                        </div>
                    </Col>
                </Row>
                <Row>
                    <Col lg={{ span: 6, offset: 3 }} md={12} sm={12} xs={12}>
                        <div className={styles.addressForm}>
                            <h2>Shipping Address #{stt + 1}</h2>
                            <form>
                                <div className={styles.formGroup}>
                                    <label htmlFor="firstName">
                                        First Name Receiver
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        value={firstName}
                                        onChange={e => setFirstName(e.target.value)}
                                        required
                                    />
                                    {errors["firstName"] && (
                                        <p className="checkInput">Invalid First Name</p>
                                    )}
                                    <label htmlFor="lastName">
                                        Last Name Receiver
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        value={lastName}
                                        onChange={e => setLastName(e.target.value)}
                                        required
                                    />
                                    {errors["lastName"] && (
                                        <p className="checkInput">Invalid Last Name</p>
                                    )}
                                    <button type="button" className='theme-btn-one bg-black btn_sm' onClick={handleUpdateName}>Update Name Receiver</button>
                                </div>
                                <div className={styles.formGroup}>
                                    <label htmlFor="streetName">
                                        Street name
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        value={streetName}
                                        onChange={e => setStreetName(e.target.value)}
                                        required
                                    />
                                    {errors["streetName"] && (
                                        <p className="checkInput">Invalid street name.</p>
                                    )}
                                    <label htmlFor="district">
                                        District
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        value={district}
                                        onChange={e => setDistrict(e.target.value)}
                                        required
                                    />
                                    {errors["district"] && (
                                        <p className="checkInput">Invalid district</p>
                                    )}
                                    <label htmlFor="ward">
                                        Ward
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        value={ward}
                                        onChange={e => setWard(e.target.value)}
                                        required
                                    />
                                    {errors["ward"] && (
                                        <p className="checkInput">Invalid ward</p>
                                    )}
                                    <label htmlFor="city">
                                        City
                                    </label>
                                    <input
                                        className="FormInput"
                                        type="text"
                                        value={city}
                                        onChange={e => setCity(e.target.value)}
                                        required
                                    />
                                    {errors["city"] && (
                                        <p className="checkInput">Invalid City</p>
                                    )}
                                    <button type="button" className='theme-btn-one bg-black btn_sm' onClick={handleUpdateName}>Update Address</button>
                                </div>
                                <div className={styles.formGroup}>
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
                                    <button type="button" className='theme-btn-one bg-black btn_sm' onClick={handleUpdateName}>Update phone number</button>
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