import React, { useState } from 'react';
import { FaRegCheckCircle, FaTimesCircle } from 'react-icons/fa';
import axios from 'axios';
import Cookies from 'js-cookie';
import styles from "../../ProductWrapper/ProductWrapper.module.css"

const ModalAddToCart = ({ productId }) => {
    const [modal, setModal] = useState(false);
    const [message, setMessage] = useState('');
    const [success, setSuccess] = useState('');

    const handleAddToCart = (productId) => {

        axios
            .post(`http://localhost:8000/api/user/cart/add/${productId}`, [], {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then(response => {
                setSuccess(response.data.success)
                setMessage(response.data.message)
                setModal(!modal);

            })
            .catch(error => {
                console.log(error);
            });
    }

    const closeModal = () => {
        setModal(!modal);
    }

    if (modal) {
        document.body.classList.add('active-modal')
    } else {
        document.body.classList.remove('active-modal')
    }

    return (
        <>
            <button className={`${styles.addToCart}`} onClick={() => handleAddToCart(productId)}>Thêm vào giỏ hàng</button>

            {modal && (
                <div className="modal">
                    <div onClick={closeModal} className="overlay"></div>
                    <div className="modal-content">
                        <div>
                            {success === true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                        </div>
                        <h2 className="title_modal">Thêm vào giỏ hàng {success ? 'thành công' : 'thất bại'}</h2>
                        <p className='p_modal'>{success ? '' : message}</p>
                        <div className='divClose'>
                            <button className="close close-modal" onClick={closeModal}>OK</button>
                        </div>
                    </div>
                </div>
            )}
        </>
    )
}

export default ModalAddToCart