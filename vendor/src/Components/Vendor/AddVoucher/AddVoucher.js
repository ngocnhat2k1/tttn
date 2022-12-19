import React, { useEffect, useState } from 'react'
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import '../DashBoard.css'
import { useForm } from "react-hook-form";
import axios from 'axios';
import Cookies from 'js-cookie';
import formatDate from '../../../until/formatDateTime';
import ModalConfirm from '../ModalConfirm/ModalConfirm';
const AddVoucher = () => {
    const [success, setSuccess] = useState("")
    const [message, setMessage] = useState('')
    const [notify, setNotify] = useState(false)

    const { register, handleSubmit, watch, formState: { errors } } = useForm();
    const onSubmit = data => {
        const payload = {
            ...data,
            expiredDate: formatDate(data.expiredDate)
        }
        axios
            .post('http://127.0.0.1:8000/api/v1/vouchers/create', payload,
                {
                    headers: {
                        Authorization: `Bearer ${Cookies.get('adminToken')}`,
                    },
                },
            )
            .then((response) => {
                setSuccess(response.data.success);
                setMessage(response.data.message);
                setNotify(true)
            })
            .catch(function (error) {
                alert(error);
                console.log(error);
            });
    }

    return (
        <Col sm={12} md={12} lg={9}>
            <div className='tab-content dashboard_content'>
                <div className='tab-pane fade show active'>
                    <div id='add_product_area'>
                        <div className='container'>
                            <Row>
                                <Col lg={12}>
                                    <div className='add_product_wrapper'>
                                        <h4>Thêm mã giảm giá</h4>
                                        <form className='add_product_form'
                                            onSubmit={handleSubmit(onSubmit)}>
                                            <Row>

                                                <Col lg={6}>
                                                    <div className='fotm-group'>
                                                        <label htmlFor="voucher_name">Mã giảm giá</label>
                                                        <input
                                                            id='voucher_name'
                                                            type="text"
                                                            className='form-control'
                                                            placeholder='Tên mã giảm giá'
                                                            {...register("name", { required: true })} />
                                                        {errors.name?.type && <span className='error'>Không được bỏ trống mục này</span>}
                                                    </div>
                                                </Col>

                                                <Col lg={6}>
                                                    <div className='fotm-group'>
                                                        <label htmlFor="percent">Phần trăm giảm giá</label>
                                                        <input
                                                            id='percent'
                                                            type="number"
                                                            className='form-control'
                                                            placeholder='Phần trăm giảm giá'
                                                            {...register("percent", { required: true, min: 1, max: 99 })} />
                                                        {errors.percent && (errors.percent.type === 'min' || errors.percent.type === 'max') && <span className='error'>Phần trăm giảm giá chỉ có thể từ 1-99</span>}
                                                        {errors.percent && errors.percent.type === 'required' && <span className='error'>Không được bỏ trống mục này</span>}
                                                    </div>
                                                </Col>

                                                <Col lg={6}>
                                                    <div className='fotm-group'>
                                                        <label htmlFor="usage">Lựot sử dụng</label>
                                                        <input
                                                            id='usage'
                                                            type="number"
                                                            className='form-control'
                                                            placeholder='Lượt sử dụng'
                                                            {...register("usage", { required: true, min: 1 })} />
                                                        {errors.usage && errors.usage.type === 'min' && <span className='error'>Lượt sử dụng phải lớn hơn 1</span>}
                                                        {errors.usage && errors.usage.type === 'required' && <span className='error'>Không được bỏ trống mục này</span>}

                                                    </div>
                                                </Col>

                                                <Col lg={6}>
                                                    <div className='fotm-group'>
                                                        <label htmlFor="expiredDate">Ngày Hết Hạn</label>
                                                        <input
                                                            id='expiredDate'
                                                            type="datetime-local"
                                                            className='form-control'
                                                            {...register("expiredDate", { required: true })} />
                                                        {errors.expiredDate && errors.expiredDate.type === 'required' && <span className='error'>Không được bỏ trống mục này</span>}
                                                    </div>
                                                </Col>

                                                <Col lg={12}>
                                                    <div className='vendor_order_boxed position-relative'>
                                                        <div className='btn_right_table'>
                                                            <button type='submit' className="theme-btn-one bg-black btn_sm">
                                                                Thêm mã giảm giá
                                                            </button>
                                                        </div>
                                                    </div>
                                                </Col>
                                            </Row>
                                        </form>
                                    </div>

                                </Col>
                            </Row>
                        </div>
                    </div>
                </div>
            </div>
            {notify && (
                <ModalConfirm success={success} message={message} />
            )}
        </Col >
    )
}

export default AddVoucher