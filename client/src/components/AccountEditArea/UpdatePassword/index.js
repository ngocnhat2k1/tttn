import { useForm } from "react-hook-form";
import axios from "axios";
import { useState } from 'react';
import Cookies from 'js-cookie';
import styles from "../../AccountEditArea/AccountEditArea.module.css"
import { FaRegCheckCircle, FaTimesCircle } from 'react-icons/fa'

function UpdatePassword() {
    const [message, setMessage] = useState('');
    const [success, setSuccess] = useState('');
    const [modal, setModal] = useState(false);

    const { register, handleSubmit, formState: { errors }, reset } = useForm();

    const closeModal = () => {
        setModal(!modal);
    }

    const handleUpdatePassword = (data) => {
        axios
            .put(`http://localhost:8000/api/user/changePassword`, data, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then(function (response) {
                if (response.data.success) {
                    setSuccess(response.data.success)
                    setMessage(response.data.message)
                    reset()
                    setModal(!modal);
                } else {
                    setSuccess(response.data.success)
                    setMessage(response.data.errors);
                    setModal(!modal);
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    if (modal) {
        document.body.classList.add('active-modal')
    } else {
        document.body.classList.remove('active-modal')
    }

    return (
        <form onSubmit={handleSubmit(handleUpdatePassword)} id='accountEditFormPassword' className={styles.accountEditForm}>
            <div className={styles.formGroup}>
                <label htmlFor="oldPassword">Mật khẩu hiện tại
                    <span className="text-danger">*</span>
                </label>
                <input
                    className="FormInput"
                    type="password"
                    placeholder="Mật khẩu hiện tại"
                    {...register("oldPassword", { required: true, minLength: 6, maxLength: 24 })}
                />
                {errors.oldPassword && errors.oldPassword.type === "required" && (
                    <p className="checkInput">Mật khẩu hiện tại không được để trống</p>
                )}
                {errors.oldPassword && errors.oldPassword.type === "minLength" && (
                    <p className="checkInput">Mật khẩu hiện tại phải có ít nhất 6 ký tự</p>
                )}
                {errors.oldPassword && errors.oldPassword.type === "maxLength" && (
                    <p className="checkInput">Mật khẩu hiện tại chỉ được tối đa 24 ký tự</p>
                )}
                <label htmlFor="password">Mật khẩu mới</label>
                <input
                    className="FormInput"
                    type="password"
                    placeholder="Mật khẩu mới"
                    {...register("password", { required: true, minLength: 6, maxLength: 24 })}
                />
                {errors.password && errors.password.type === "required" && (
                    <p className="checkInput">Mật khẩu mới không được để trống</p>
                )}
                {errors.password && errors.password.type === "minLength" && (
                    <p className="checkInput">Mật khẩu mới phải có ít nhất 6 ký tự</p>
                )}
                {errors.password && errors.password.type === "maxLength" && (
                    <p className="checkInput">Mật khẩu mới chỉ được tối đa 24 ký tự</p>
                )}
                <label htmlFor="confirmPassword">Xác nhận mật khẩu mới</label>
                <input
                    className="FormInput"
                    type="password"
                    placeholder="Xác nhận mật khẩu mới"
                    {...register("confirmPassword", { required: true, minLength: 6, maxLength: 24 })}
                />
                {errors.confirmPassword && errors.confirmPassword.type === "required" && (
                    <p className="checkInput">Xác nhận mật khẩu mới không được để trống</p>
                )}
                {errors.confirmPassword && errors.confirmPassword.type === "minLength" && (
                    <p className="checkInput">Xác nhận mật khẩu mới phải có ít nhất 6 ký tự</p>
                )}
                {errors.confirmPassword && errors.confirmPassword.type === "maxLength" && (
                    <p className="checkInput">Xác nhận mật khẩu mới chỉ được tối đa 24 ký tự</p>
                )}
            </div>
            <button type="submit" className="theme-btn-one bg-black btn_sm">Cập nhật mật khẩu</button>

            {modal && (
                <div className="modal">
                    <div onClick={closeModal} className="overlay"></div>
                    <div className="modal-content">
                        <div>
                            {success === true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                        </div>
                        <h2 className="title_modal">Cập nhật mật khẩu {success ? 'thành công' : 'thất bại'}</h2>
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

export default UpdatePassword