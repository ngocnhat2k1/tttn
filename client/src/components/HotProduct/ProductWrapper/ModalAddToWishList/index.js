import { useState } from 'react';
import "../../../ModalATag/Modal.css"
import { FaRegHeart, FaRegCheckCircle, FaTimesCircle } from 'react-icons/fa'
import styles from "../../ProductWrapper/ProductWrapper.module.css"
import axios from 'axios';
import Cookies from 'js-cookie';

const ModalAddToWishList = ({ productId }) => {
    const [modal, setModal] = useState(false);
    const [message, setMessage] = useState('');
    const [success, setSuccess] = useState('');

    const closeModal = () => {
        setModal(!modal);
    }

    const handleAddToWishList = (productId) => {

        axios
            .get(`http://localhost:8000/api/retrieveToken`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                if (!response.data.success) {
                    window.location.href = 'http://localhost:3000/login';
                } else {
                    axios
                        .post(`http://localhost:8000/api/user/favorite/${productId}`, [], {
                            headers: {
                                Authorization: `Bearer ${Cookies.get('token')}`,
                            },
                        })
                        .then(response => {
                            setMessage(response.data.message);
                            setSuccess(response.data.success);
                            setModal(!modal);
                        })
                        .catch(err => {
                            console.log(err);
                        })
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
        <>
            <a className={`${styles.wishList} ${styles.action}`} title="Wishlist" onClick={() => handleAddToWishList(productId)}>
                <FaRegHeart />
            </a>

            {modal && (
                <div className="modal">
                    <div onClick={closeModal} className="overlay"></div>
                    <div className="modal-content">
                        <div>
                            {success === true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                        </div>
                        <h2 className="title_modal">Thêm vào danh sách yêu thích {success ? 'thành công' : 'thất bại'}</h2>
                        <p className='p_modal'>{message}</p>
                        <div className='divClose'>
                            <button className="close close-modal" onClick={closeModal}>OK</button>
                        </div>
                    </div>
                </div>
            )}
        </>
    )
}

export default ModalAddToWishList