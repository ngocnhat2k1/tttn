import React, { useEffect, useState } from 'react'
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import '../DashBoard.css'
import { useForm } from "react-hook-form";
import axios from 'axios';
import Cookies from 'js-cookie';
import formatDate from '../../../until/formatDateTime';
const AddVoucher = () => {
    const { register, handleSubmit, watch, formState: { errors } } = useForm();
    const onSubmit = data => {
        const payload = {
            ...data,
            expiredDate: formatDate(data.expiredDate)
        }
        console.log(payload)
        axios
            .post('http://127.0.0.1:8000/api/v1/vouchers/create', payload,
                {
                    headers: {
                        Authorization: `Bearer ${Cookies.get('adminToken')}`,
                    },
                },
            )
            .then((response) => {
                alert(response.data.success);
                console.log(response.data.error);
                if (response.data.success === true) {
                    window.location.reload(false)
                }
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
                                        <h4>Add Voucher</h4>
                                        <form className='add_product_form'
                                            onSubmit={handleSubmit(onSubmit)}>
                                            <Row>

                                                <Col lg={6}>
                                                    <div className='fotm-group'>
                                                        <label htmlFor="voucher_name">Voucher Name</label>
                                                        <input
                                                            id='voucher_name'
                                                            type="text"
                                                            className='form-control'
                                                            placeholder='Voucher Title here'
                                                            {...register("name", { required: true })} />
                                                        {errors.name?.type && <span className='error'>Không được bỏ trống mục này</span>}
                                                    </div>
                                                </Col>

                                                <Col lg={6}>
                                                    <div className='fotm-group'>
                                                        <label htmlFor="percent_sale">Percent Sale</label>
                                                        <input
                                                            id='percent_sale'
                                                            type="number"
                                                            className='form-control'
                                                            placeholder='Percent Sale here'
                                                            {...register("percent", { required: true }, { min: 1, max: 99 })} />
                                                        {errors.name?.type && <span className='error'>Không được bỏ trống mục này</span>}
                                                    </div>
                                                </Col>

                                                <Col lg={6}>
                                                    <div className='fotm-group'>
                                                        <label htmlFor="usage">Voucher Usage</label>
                                                        <input
                                                            id='usage'
                                                            type="number"
                                                            className='form-control'
                                                            placeholder='Voucher Usage here'
                                                            {...register("usage", { required: true }, { min: 1 })} />
                                                        {errors.name?.type && <span className='error'>Không được bỏ trống mục này</span>}
                                                    </div>
                                                </Col>

                                                <Col lg={6}>
                                                    <div className='fotm-group'>
                                                        <label htmlFor="expiredDate">Expired Date</label>
                                                        <input
                                                            id='expiredDate'
                                                            type="datetime-local"
                                                            className='form-control'
                                                            placeholder='Expired Date here'
                                                            {...register("expiredDate", { required: true })} />
                                                        {errors.name?.type && <span className='error'>Không được bỏ trống mục này</span>}
                                                    </div>
                                                </Col>

                                                <Col lg={12}>
                                                    <div className='vendor_order_boxed position-relative'>
                                                        <div className='btn_right_table'>
                                                            <button type='submit' className="theme-btn-one bg-black btn_sm">
                                                                Add Product
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
        </Col >
    )
}

export default AddVoucher