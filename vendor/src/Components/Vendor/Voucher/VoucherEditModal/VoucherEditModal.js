import React, { useState } from "react";
import { FaEdit, FaTimes } from 'react-icons/fa'
import axios from 'axios';
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import Cookies from 'js-cookie';
import { useForm } from "react-hook-form";
import "./Modal.css";
import 'bootstrap/dist/css/bootstrap.min.css';

const VoucherEditModal = ({ idDetail }) => {
    const [modal, setModal] = useState(false);

    const [voucherName, setVoucherName] = useState('')
    const [voucherPercent, setVoucherPercent] = useState('')
    const [voucherUsage, setVoucherUsage] = useState('')
    const [VoucherExpiredDate, setVoucherexpiredDate] = useState('')
    const [deleted, setDeleted] = useState('')

    const { register, handleSubmit, watch, formState: { errors } } = useForm();
    const toggleModal = () => {
        setModal(!modal);
        axios
            .get(`http://127.0.0.1:8000/api/v1/vouchers/${idDetail}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`,
                },
            })

            .then((response) => {
                setVoucherName(response.data.name);
                setVoucherPercent(response.data.percent)
                setVoucherUsage(response.data.usage)
                setVoucherexpiredDate(response.data.expiredDate)
                setDeleted(response.data.deleted)
            });
    };
    const reversedVoucher = () => {
        axios
            .delete(`http://127.0.0.1:8000/api/v1/vouchers/${idDetail}/destroy=0`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`,
                },
            })
            .then((response) => {
                alert(response.data.message)
                if (response.data.success === true) {
                    window.location.reload();
                }
            })
    }
    if (modal) {
        document.body.classList.add('active-modal')
    } else {
        document.body.classList.remove('active-modal')
    }
    const closeModal = () => {
        setModal(!modal)
    }
    const onSubmit = (data) => {
        console.log(data)
        axios
            .put(`http://127.0.0.1:8000/api/v1/voucher/${idDetail}/update`, data, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`
                },
            })
            .then((response) => {
                alert(response.data.success);
                console.log(response.data.error);
                if (response.data.success === true) {
                    window.location.reload = (false);
                }
            })
            .catch(function (error) {
                alert(error);
                console.log(error);
            });
    }
    const onChangeName = (e) => {
        setVoucherName(e.target.value)
    }
    const onChangePercent = (e) => {
        setVoucherPercent(e.target.value)
    }
    const onChangeUsage = (e) => {
        setVoucherUsage(e.target.value)
    }
    const onChangeDate = (e) => {
        setVoucherexpiredDate(e.target.value)
    }

    return (
        <>
            <FaEdit onClick={toggleModal} className="btn-modal">
            </FaEdit>

            {modal && (
                <div className="modal">
                    <div onClick={toggleModal} className="overlay"></div>
                    <div className="modal-content-edit-voucher">
                        <h2 className="title_modal">Edit Voucher {idDetail}</h2>
                        <form onSubmit={handleSubmit(onSubmit)}>
                            <Row>
                                <Col lg={6}>
                                    <div className="fotm-group">
                                        <label htmlFor="name">Voucher Name</label>
                                        <input type="text"
                                            className="form-control"
                                            id="name"
                                            value={voucherName}
                                            {...register('name', { required: true, onChange: onChangeName })} />
                                    </div>
                                </Col>
                                <Col lg={6}>
                                    <div className="fotm-group">
                                        <label htmlFor="percent">Voucher Percent</label>
                                        <input type="number"
                                            className="form-control"
                                            id="percent"
                                            value={voucherPercent}
                                            {...register('percent', { required: true, onChange: onChangePercent })} />
                                    </div>
                                </Col>
                                <Col lg={6}>
                                    <div className="fotm-group">
                                        <label htmlFor="usage">Voucher Usage</label>
                                        <input type="number"
                                            className="form-control"
                                            id="usage"
                                            value={voucherUsage}
                                            {...register('usage', { required: true, onChange: onChangeUsage })} />
                                    </div>
                                </Col>
                                <Col lg={6}>
                                    <div className="fotm-group">
                                        <label htmlFor="VoucherExpiredDate">Voucher Expired Date</label>
                                        <input type="datetime-local"
                                            className="form-control"
                                            id="VoucherExpiredDate"
                                            value={VoucherExpiredDate}
                                            {...register('expiredDate', { required: true, onChange: onChangeDate })} />
                                    </div>
                                </Col>

                            </Row>
                            <Col lg={12}>
                                {deleted ?
                                    <div className="btn_left_table" onClick={reversedVoucher}>
                                        <button className="theme-btn-one bg-black btn_sm">Restore</button>
                                    </div> : ""}
                                <div className="btn_right_table">
                                    <button className="theme-btn-one bg-black btn_sm">Save</button>
                                </div>
                            </Col>
                        </form>

                        <button className="close close-modal" onClick={closeModal}><FaTimes /></button>

                    </div>
                </div>
            )
            }
        </>
    )
}

export default VoucherEditModal