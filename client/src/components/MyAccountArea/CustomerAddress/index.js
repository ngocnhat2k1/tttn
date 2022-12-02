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
            <Col lg={12} md={12} sm={12} xs={12}>
                <h4 className='textRight p-3'>
                    <Link className="theme-btn-one bg-black btn_sm p-3" to="/address-create">
                        THÊM ĐỊA CHỈ MỚI
                    </Link>
                </h4>
            </Col>
            {listAddress.map((address, index) => {
                return (
                    <Col lg={6} key={index}>
                        <div className={styles.myaccountContent}>
                            <h4 className={styles.title}>Địa chỉ giao hàng {index + 1}</h4>
                            <div className={styles.shippingAddress}>
                                <h5>
                                    <strong>{address.nameReceiver}</strong>
                                </h5>
                                <p>
                                    {address.streetName}, {address.ward}<br />
                                    {address.district}, {address.city}
                                </p>
                                <p>Số điện thoại: {address.phoneReceiver}</p>
                                <Link to={`/address-edit/${address.id}`} className='theme-btn-one bg-black btn_sm mt-4'>Cập nhật địa chỉ</Link>
                            </div>
                        </div>
                    </Col>
                )
            })}
        </Row>
    )
}

export default CustomerAddress