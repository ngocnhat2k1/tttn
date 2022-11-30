import styles from '../MyAccountArea.module.scss';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import axios from 'axios';
import { useState, useEffect } from 'react';
import Cookies from 'js-cookie';
import { Link } from 'react-router-dom'

function CustomerAddress() {

    const [listAddress, setListAddress] = useState([]);

    useEffect(() => {
        axios
            .get(`http://localhost:8000/api/user/address`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then(response => {
                    setListAddress(response.data.data);
            })
            .catch(function (error) {
                console.log(error);
            });
    }, []);

    return (
        <Row>
            {listAddress.map((address, index) => {
                return (
                    <Col lg={6} key={index}>
                        <div className={styles.myaccountContent}>
                            <h4 className={styles.title}>Shipping Address {index + 1}</h4>
                            <div className={styles.shippingAddress}>
                                <h5>
                                    <strong>{address.nameReceiver}</strong>
                                </h5>
                                <p>
                                    {address.streetName}, {address.district}<br />
                                    {address.ward}, {address.city}
                                </p>
                                <p>Mobile: {address.phoneReceiver}</p>
                                <Link to={`/address-edit/${address.id}`} className='theme-btn-one bg-black btn_sm mt-4'>Edit Address</Link>
                            </div>
                        </div>
                    </Col>
                )
            })}
        </Row>
    )
}

export default CustomerAddress