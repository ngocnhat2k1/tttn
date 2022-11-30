import { useForm } from "react-hook-form";
import axios from "axios";
import { useState, useEffect } from 'react';
import Cookies from 'js-cookie';
import styles from "../../AccountEditArea/AccountEditArea.module.css"
import { FaRegCheckCircle, FaTimesCircle } from 'react-icons/fa'
import { FaCamera } from 'react-icons/fa';

function UpdateAvatar({ avt }) {
    const [message, setMessage] = useState('');
    const [success, setSuccess] = useState('');
    const [avatar, setAvatar] = useState('');
    const [modal, setModal] = useState(false);

    const { register, handleSubmit, reset } = useForm();

    useEffect(() => {
        setAvatar(avt   )
        reset({
            avatar: avt
        })
    }, [avt])

    const closeModal = () => {
        setModal(!modal);
    }

    const handleImage = (e) => {
        const file = e.target.files[0];

        const Reader = new FileReader();

        Reader.readAsDataURL(file);

        Reader.onload = () => {
            if (Reader.readyState === 2) {
                setAvatar(Reader.result);
            }
        };
    }

    const handleUpdateAvatar = (data) => {

        const payload = {
            avatar: avatar,
        }

        axios
            .put(`http://localhost:8000/api/user/avatar/upload`, payload, {
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
                console.log(error);
            });
    }

    if (modal) {
        document.body.classList.add('active-modal')
    } else {
        document.body.classList.remove('active-modal')
    }

    return (
        <div className={styles.accountThumd}>
            <form onSubmit={handleSubmit(handleUpdateAvatar)}>
                <div className={styles.accountThumbImg}>
                    <img src={avatar} alt="img" />
                    <div className={styles.fixedIcon}>
                        <input
                            className="FormInput"
                            type="file"
                            accept='image/*'
                            {...register("avatar", { onChange: handleImage })}
                        /><FaCamera />
                    </div>
                </div>
                <div className='m-4 mx-auto'>
                    <button type="submit" className="theme-btn-one bg-black btn_sm">Cập nhật ảnh đại diện</button>

                    {modal && (
                        <div className="modal">
                            <div onClick={closeModal} className="overlay"></div>
                            <div className="modal-content">
                                <div>
                                    {success === true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                                </div>
                                <h2 className="title_modal">Cập nhật ảnh đại diện {success ? 'thành công' : 'thất bại'}</h2>
                                <p className='p_modal'>{message}</p>
                                <div className='divClose'>
                                    <button className="close close-modal" onClick={closeModal}>OK</button>
                                </div>

                            </div>
                        </div>
                    )}
                </div>
            </form>
        </div>
    )
}

export default UpdateAvatar