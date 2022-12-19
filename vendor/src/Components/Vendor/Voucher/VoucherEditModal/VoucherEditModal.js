import React, { useState } from "react";
import { FaEdit, FaTimes, FaRegCheckCircle, FaTimesCircle } from 'react-icons/fa'
import axios from 'axios';
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import Cookies from 'js-cookie';
import { useForm } from "react-hook-form";
import "../../Modal.css";
import 'bootstrap/dist/css/bootstrap.min.css';
import formatDate from "../../../../until/formatDateTime";

const VoucherEditModal = ({ idDetail }) => {
    const [modal, setModal] = useState(false);
    const [voucherName, setVoucherName] = useState('')
    const [voucherPercent, setVoucherPercent] = useState('')
    const [voucherUsage, setVoucherUsage] = useState('')
    const [VoucherExpiredDate, setVoucherexpiredDate] = useState('')
    const [deleted, setDeleted] = useState('')
    const [isChange, setIsChange] = useState(false)
    const [success, setSuccess] = useState("")
    const [message, setMessage] = useState('')
    const voucherInsessicon = sessionStorage.getItem("voucher");
    const [notify, setNotify] = useState(false)
    const { register, handleSubmit, watch, formState: { errors }, reset } = useForm();
    const toggleModal = () => {
        setModal(!modal);
        axios
            .get(`http://127.0.0.1:8000/api/v1/vouchers/${idDetail}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`,
                },
            })

            .then((response) => {
                reset(response.data.data)
                sessionStorage.setItem("voucher", JSON.stringify(response.data.data))
                setVoucherName(response.data.data.name);
                setVoucherPercent(response.data.data.percent)
                setVoucherUsage(response.data.data.usage)
                setVoucherexpiredDate(response.data.data.expiredDate)
                setDeleted(response.data.data.deleted)
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
                setSuccess(response.data.success)
                if (response.data.success) {
                    setMessage(response.data.errors)
                } else {
                    setMessage(response.data.message)
                }

                setNotify(true)
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
        const payload = {
            ...data,
            expiredDate: formatDate(data.expiredDate)
        }
        let { deleted, updateAt, voucherID, createdAt, ...rest } = payload
        console.log(payload)
        axios
            .put(`http://127.0.0.1:8000/api/v1/vouchers/${idDetail}/update`, rest, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('adminToken')}`
                },
            })
            .then((response) => {
                setSuccess(response.data.success)
                if (response.data.success) {
                    setMessage(response.data.errors)
                } else {
                    setMessage(response.data.message)
                }

                setNotify(true)
            })
            .catch(function (error) {
                alert(error);
                console.log(error);
            });
    }
    const closeNotify = () => {
        setNotify(!notify);
        if (!success) {
            window.location.reload(false)
        }
    }
    return (
        <>
            <FaEdit onClick={toggleModal} className="btn-modal">
            </FaEdit>

            {modal && (
                <div className="modal">
                    <div onClick={toggleModal} className="overlay"></div>
                    <div className="modal-content-edit-voucher">
                        <h2 className="title_modal">Chỉnh sửa mã giảm giá</h2>
                        <form onSubmit={handleSubmit(onSubmit)}>
                            <Row>
                                <Col lg={6}>
                                    <div className="fotm-group">
                                        <label htmlFor="name">Mã giảm giá</label>
                                        <input type="text"
                                            className="form-control"
                                            id="name"
                                            {...register('name', {
                                                required: true,
                                                onChange: (e) => {
                                                    setVoucherName(e.target.value)
                                                    if (voucherName == JSON.parse(voucherInsessicon).name) {
                                                        setIsChange(true)
                                                    }
                                                }
                                            })} />
                                        {errors.name?.type && <span className='error'>Không được bỏ trống mục này</span>}
                                    </div>
                                </Col>
                                <Col lg={6}>
                                    <div className="fotm-group">
                                        <label htmlFor="percent">Phần trăm giảm giá</label>
                                        <input type="number"
                                            className="form-control"
                                            id="percent"
                                            {...register('percent', {
                                                required: true,
                                                min: 1, max: 99,
                                                onChange: (e) => {
                                                    setVoucherPercent(e.target.value)
                                                    if (voucherPercent == JSON.parse(voucherInsessicon).percent) {
                                                        setIsChange(true)
                                                    }
                                                }
                                            })} />
                                        {errors.percent && (errors.percent.type === 'min' || errors.percent.type === 'max') && <span className='error'>Phần trăm giảm giá chỉ có thể từ 1-99</span>}
                                        {errors.percent && errors.percent.type === 'required' && <span className='error'>Không được bỏ trống mục này</span>}
                                    </div>
                                </Col>
                                <Col lg={6}>
                                    <div className="fotm-group">
                                        <label htmlFor="usage">Lượt sử dụng</label>
                                        <input type="number"
                                            className="form-control"
                                            id="usage"
                                            {...register('usage', {
                                                required: true,
                                                min: 1,
                                                onChange: (e) => {
                                                    setVoucherUsage(e.target.value)
                                                    if (voucherUsage == JSON.parse(voucherInsessicon).usage) {
                                                        setIsChange(true)
                                                    }
                                                }
                                            })} />
                                        {errors.usage && errors.usage.type === 'min' && <span className='error'>Lượt sử dụng phải lớn hơn 1</span>}
                                        {errors.usage && errors.usage.type === 'required' && <span className='error'>Không được bỏ trống mục này</span>}
                                    </div>
                                </Col>
                                <Col lg={6}>
                                    <div className="fotm-group">
                                        <label htmlFor="VoucherExpiredDate">Ngày hết hạn</label>
                                        <input type="datetime-local"
                                            className="form-control"
                                            id="VoucherExpiredDate"
                                            value={VoucherExpiredDate}
                                            {...register('expiredDate', {
                                                required: true,
                                                onChange: (e) => {
                                                    setVoucherexpiredDate(e.target.value)
                                                    if (VoucherExpiredDate == JSON.parse(voucherInsessicon).expiredDate) {
                                                        setIsChange(true)
                                                    }
                                                }
                                            })} />
                                        {errors.expiredDate && errors.expiredDate.type === 'required' && <span className='error'>Không được bỏ trống mục này</span>}
                                    </div>
                                </Col>
                            </Row>
                            <Col lg={12}>
                                {deleted ?
                                    <div className="btn_left_table" onClick={reversedVoucher}>
                                        <button className="theme-btn-one bg-black btn_sm">Khôi phục</button>
                                    </div> : ""}
                                <div className="btn_right_table">
                                    {isChange ? <button className="theme-btn-one bg-black btn_sm">Lưu</button> : <button className="theme-btn-one bg-black btn_sm btn btn-secondary btn-lg" disabled>Lưu</button>}
                                </div>
                            </Col>
                        </form>
                        <button className="close close-modal" onClick={closeModal}><FaTimes /></button>
                    </div>
                </div>
            )
            }
            {notify && (
                <div className="modal">
                    <div onClick={closeNotify} className="overlay"></div>
                    <div className="modal-content">
                        <div>
                            {success == true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                        </div>
                        <h2 className="title_modal">{success ? 'Successful' : 'Failed'}</h2>
                        <p className='p_modal'>{message}</p>
                        <div className="btn_right_table">
                            <button className="theme-btn-one bg-black btn_sm" onClick={closeNotify}>Close </button>
                        </div>
                        <div className='divClose'>
                            <button className="close close-modal" onClick={closeNotify}><FaTimes /></button>
                        </div>
                    </div>
                </div>
            )}
        </>
    )
}

export default VoucherEditModal