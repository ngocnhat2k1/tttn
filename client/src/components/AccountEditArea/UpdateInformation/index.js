import { useForm } from "react-hook-form";
import axios from "axios";
import { useState, useEffect } from 'react';
import Cookies from 'js-cookie';
import styles from "../../AccountEditArea/AccountEditArea.module.css"
import { FaRegCheckCircle, FaTimesCircle } from 'react-icons/fa'

function UpdateInformation({ em, fName, lName }) {
    const [message, setMessage] = useState('');
    const [success, setSuccess] = useState('');
    const [modal, setModal] = useState(false);

    const { register, handleSubmit, formState: { errors }, reset } = useForm();

    useEffect(() => {
        reset({
            firstName: fName,
            lastName: lName,
            email: em
        })
    }, [em, fName, lName])

    const closeModal = () => {
        setModal(!modal);
    }

    if (modal) {
        document.body.classList.add('active-modal')
    } else {
        document.body.classList.remove('active-modal')
    }

    const handleUpdateInformation = (data) => {

        axios
            .put(`http://localhost:8000/api/user/update`, data, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then(function (response) {
                if (response.data.success) {
                    setSuccess(response.data.success)
                    setMessage(response.data.message)
                    setModal(!modal)
                } else {
                    setSuccess(response.data.success)
                    setMessage(response.data.message);
                    setModal(!modal)
                }
            })
            .catch(function (error) {
            });
    };

    return (
        <form onSubmit={handleSubmit(handleUpdateInformation)} id='accountEditFormInformation' className={styles.accountEditForm}>
            <div className={styles.formGroup}>
                <label htmlFor="firstName">Họ</label>
                <input
                    className="FormInput"
                    type="text"
                    placeholder="VD: Lê Quốc"
                    {...register("firstName", { required: true, minLength: 2, maxLength: 50 })}
                />
                {errors.firstName && errors.firstName.type === "required" && (
                    <p className="checkInput">Họ không được để trống</p>
                )}
                {errors.firstName && errors.firstName.type === "minLength" && (
                    <p className="checkInput">Họ phải có ít nhất 2 ký tự</p>
                )}
                {errors.firstName && errors.firstName.type === "maxLength" && (
                    <p className="checkInput">Họ chỉ được tối đa 50 ký tự</p>
                )}
                <label htmlFor="lastName">Tên</label>
                <input
                    className="FormInput"
                    type="text"
                    placeholder="VD: Bảo"
                    {...register("lastName", { required: true, minLength: 2, maxLength: 50 })}
                />
                {errors.lastName && errors.lastName.type === "required" && (
                    <p className="checkInput">Tên không được để trống</p>
                )}
                {errors.lastName && errors.lastName.type === "minLength" && (
                    <p className="checkInput">Tên phải có ít nhất 2 ký tự</p>
                )}
                {errors.lastName && errors.lastName.type === "maxLength" && (
                    <p className="checkInput">Tên chỉ được tối đa 50 ký tự</p>
                )}
            </div>

            <div className={styles.formGroup}>
                <label htmlFor="email">Email</label>
                <input
                    className="FormInput"
                    type="text"
                    placeholder="Username or Email"
                    {...register("email", { required: true, pattern: /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/ })}
                />
                {errors.email && errors.email.type === "required" && (
                    <p className="checkInput">Email không được để trống</p>
                )}
                {errors.email && errors.email.type === "pattern" && (
                    <p className="checkInput">Email không hợp lệ</p>
                )}
            </div>
            <button type="submit" className="theme-btn-one bg-black btn_sm">Cập nhật thông tin</button>

            {modal && (
                <div className="modal">
                    <div onClick={closeModal} className="overlay"></div>
                    <div className="modal-content">
                        <div>
                            {success === true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                        </div>
                        <h2 className="title_modal">Cập nhật thông tin {success ? 'thành công' : 'thất bại'}</h2>
                        <p className='p_modal'>{message}</p>
                        <div className='divClose'>
                            <button className="close close-modal" onClick={closeModal}>OK</button>
                        </div>

                    </div>
                </div>
            )}
        </form>
    )
}

export default UpdateInformation