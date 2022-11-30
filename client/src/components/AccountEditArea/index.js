import styles from './AccountEditArea.module.css';
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { Link } from 'react-router-dom';
import { FaArrowLeft } from 'react-icons/fa';
import axios from '../../service/axiosClient';
import { useState, useEffect } from 'react';
import Cookies from 'js-cookie';
import UpdateInformation from './UpdateInformation';
import UpdateAvatar from './UpdateAvatar';
import UpdatePassword from './UpdatePassword';

function AccountEditArea() {

    const [lastName, setLastName] = useState('');
    const [firstName, setFirstName] = useState('');
    const [email, setEmail] = useState('');
    const [avatar, setAvatar] = useState('');

    useEffect(() => {
        axios
            .get(`http://localhost:8000/api/user/profile`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                setFirstName(response.data.data.firstName);
                setLastName(response.data.data.lastName);
                setEmail(response.data.data.email);
                if (!response.data.data.avatar) {
                    setAvatar(response.data.data.defaultAvatar)
                } else {
                    setAvatar(response.data.data.avatar)
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }, []);

    return (
        <section id={styles.accountEdit} className='ptb100'>
            <Container fluid>
                <Row>
                    <Col lg={6}>
                        <div className={styles.backBtn}>
                            <Link to='/my-account/customer-account-details'><FaArrowLeft /> Trở về trang cá nhân</Link>
                        </div>
                    </Col>
                </Row>
                <Row>
                    <Col lg={3}>
                        <UpdateAvatar avt={avatar} />
                    </Col>
                    <Col lg={9}>
                        <div className={styles.accountSetting}>
                            <div className={styles.accountSettingHeading}>
                                <h2>Thông tin tài khoản</h2>
                                <p>Chỉnh sửa thông tin tài khoản của bạn và thay đổi mật khẩu của bạn tại đây.</p>
                            </div>
                            <UpdateInformation fName={firstName} lName={lastName} em={email} />
                            <UpdatePassword />
                        </div>
                    </Col>
                </Row>
            </Container >
        </section >
    )
}

export default AccountEditArea;