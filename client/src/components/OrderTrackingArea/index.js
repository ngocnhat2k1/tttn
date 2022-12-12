import styles from './OrderTrackingArea.module.css'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useForm } from "react-hook-form";

function OrderTrackingArea() {
    const navigate = useNavigate();
    const {
        register,
        handleSubmit,
        formState: { errors },
    } = useForm();

    const onSubmit = (data) => {
        navigate(`/order-detail/${data.order_ID}`)
    }

    return (
        <section id='order_tracking' className='ptb-100'>
            <Container>
                <Row>
                    <Col lg={{ span: 6, offset: 3 }}>
                        <div className={styles.orderTrackingWrapper}>
                            <h4>Kiểm tra đơn hàng</h4>
                            <p className={styles.textDetail}>Để kiểm tra đơn hàng của bạn, vui lòng nhập mã đơn hàng của bạn vào ô bên dưới và nhấn nút "Kiểm tra"</p>
                            <form onSubmit={handleSubmit(onSubmit)}>
                                <div className={styles.formGroup}>
                                    <label htmlFor="order_ID">Mã đơn hàng</label>
                                    <input
                                        className="form-control"
                                        type="text"
                                        placeholder='Nhập mã đơn hàng của bạn'
                                        {...register("order_ID", { required: true })}
                                    />
                                     {errors.order_ID && errors.order_ID.type === "required" && (
                                        <p className="checkInput">Mã đơn hàng không được để trống</p>
                                    )}
                                </div>
                                <div>
                                    <button type="submit" className='theme-btn-one btn-black-overlay btn_md'>KIỂM TRA</button>
                                </div>
                            </form>
                        </div>
                    </Col>
                </Row>
            </Container>
        </section>
    )
}

export default OrderTrackingArea