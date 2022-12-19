import { useState, useEffect, useRef } from 'react'
import styles from './OfferCountdown.module.scss'
import { Link } from 'react-router-dom'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import axios from 'axios';

function OfferCountdown() {

    const [timeDays, setDays] = useState('00');
    const [timeHours, setHours] = useState('00');
    const [timeMinutes, setMinutes] = useState('00');
    const [timeSeconds, setSeconds] = useState('00');
    const [name, setName] = useState('');
    const [discount, setDiscount] = useState(0)
    const [usage, setUsage] = useState(0);

    let interval = useRef();

    const startCountDown = (date) => {
        const countDownDate = new Date(date).getTime();

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
        axios
            .get(`http://127.0.0.1:8000/api/show/voucher`)
            .then((response) => {
                if (response.data.success) {
                    setName(response.data.data.name)
                    setDiscount(response.data.data.percent)
                    startCountDown(response.data.data.expiredDate);
                    setUsage(response.data.data.usage)
                }
            })
            .catch(function (error) {
                console.log(error);
            });
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
                                {/* <h2>GIẢM GIÁ ĐẾN 40% CHO NHỮNG SẢN PHẨM MỚI</h2>
                                <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Porro quisquam, odit assumenda sit modi commodi esse necessitatibus temporibus aperiam veritatis eveniet!</p>
                                <Link to='/shop'>XEM THÊM</Link> */}
                                {(timeDays !== '00' || timeHours !== '00' || timeMinutes !== '00' || timeSeconds !== '00') && usage > 0 && <>
                                    <h2>MÃ GIẢM GIÁ: <span className={styles.name}>{name}</span></h2>
                                    <h3>GIẢM ĐẾN: <span className={styles.discount}>{discount}%</span></h3>
                                    <h3>SỐ LƯỢNG CÒN LẠI: <span className={styles.usage}>{usage}</span></h3>
                                    <p>Nhập mã giảm giá {name} để được nhận ưu đãi lên đến {discount}%</p>
                                    <Link to="/shop">MUA NGAY</Link>
                                </>
                                }
                                {((timeDays === '00' && timeHours === '00' && timeMinutes === '00' && timeSeconds === '00') || usage < 1) && <>
                                    <h2>HIỆN CỬA HÀNG CHƯA CÓ MÃ GIẢM GIÁ</h2>
                                    <p>Hãy đón chờ các chương trình giảm giá sắp tới nhé</p>
                                    <Link to="/shop">MUA NGAY</Link>
                                </>}
                            </div>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default OfferCountdown