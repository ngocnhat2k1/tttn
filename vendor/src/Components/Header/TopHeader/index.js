import styles from './TopHeader.module.scss'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import TopHeaderRightDefault from './TopHeaderRightDefault';
import TopHeaderRightAuth from './TopHeaderRightAuth';
import { Link } from 'react-router-dom';
import { useState, useEffect } from 'react';
import Cookies from 'js-cookie';
import axios from '../../../service/axiosClient'

function TopHeader() {
    const [user, setUser] = useState();

    useEffect(() => {
        if (Cookies.get('adminToken') !== undefined) {
            axios
                .get(`http://127.0.0.1:8000/api/admin/profile`, {
                    headers: {
                        Authorization: `Bearer ${Cookies.get('adminToken')}`,
                    },
                })
                .then((response) => {
                    setUser(response.data);
                })
                .catch(function (error) {
                    console.log('error')
                });
        }
    }, []);
    return (
        <section className={styles.divTopheader}>
            <Container className={styles.container}>
                <Row>
                    <Col lg={6} md={6} sm={12} xs={12}>
                        <div className={styles.divLeft}>
                            <p>Special collection already available. <Link to='/shop'>Read more...</Link></p>
                        </div>
                    </Col>
                    <Col lg={6} md={6} sm={12} xs={12}>
                        {user && <TopHeaderRightAuth user={user} />}
                        {!user && <TopHeaderRightDefault />}
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default TopHeader