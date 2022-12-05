import { useState, useEffect, useRef } from 'react'
import styles from './OfferCountdown.module.scss'
import { Link } from 'react-router-dom'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';

function OfferCountdown() {

    const [timeDays, setDays] = useState('00');
    const [timeHours, setHours] = useState('00');
    const [timeMinutes, setMinutes] = useState('00');
    const [timeSeconds, setSeconds] = useState('00');

    let interval = useRef();

    const startCountDown = () => {
        const countDownDate = new Date('October 1, 2022 00:00:00').getTime();

        interval = setInterval(() => {
            const now = new Date().getTime();
            const distance = countDownDate - now;

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor(distance % (1000 * 60 * 60 * 24) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor(distance % (1000 * 60) / 1000);

            if (distance < 0) {
                clearInterval(interval.current);
            } else {
                setDays(days);
                setHours(hours);
                setMinutes(minutes);
                setSeconds(seconds);
            }
        }, 1000);
    }

    useEffect(() => {
        startCountDown();
        return () => {
            clearInterval(interval.current);
        }
    }, [])

    return (
        <section id={styles.offerTime}>
            <Container>
                <Row className={styles.row}>
                    <Col className={`${styles.col}`} lg={{ span: 8, offset: 4 }} md={{ span: 7, offset: 4 }} sm={12} xs={12}>
                        <div className={`${styles.offerTimeFlex}`}>
                            <div className={styles.countDown}>
                                <div>
                                    <ul>
                                        <li>
                                            <span>{timeDays}</span> Ngày
                                        </li>
                                        <li>
                                            <span>{timeHours}</span> Giờ
                                        </li>
                                        <li>
                                            <span>{timeMinutes}</span> Phút
                                        </li>
                                        <li>
                                            <span>{timeSeconds}</span> Giây
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div className={styles.offerTimeText}>
                                <h2>GIẢM GIÁ ĐẾN 40% CHO NHỮNG SẢN PHẨM MỚI</h2>
                                <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Porro quisquam, odit assumenda sit modi commodi esse necessitatibus temporibus aperiam veritatis eveniet!</p>
                                <Link to='/shop'>XEM THÊM</Link>
                            </div>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default OfferCountdown