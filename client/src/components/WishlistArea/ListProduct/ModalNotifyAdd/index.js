import React, { useState } from 'react';
import { FaRegCheckCircle, FaTimesCircle } from 'react-icons/fa';
import axios from 'axios';
import Cookies from 'js-cookie';

const ModalNotifyAdd = ({ nameBtn, productId }) => {
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
                if (response.data.success) {
                    axios
                        .delete(`http://localhost:8000/api/user/favorite/destroy/${productId}`, {
                            headers: {
                                Authorization: `Bearer ${Cookies.get('token')}`,
                            },
                        })
                        .then(result => {
                            setSuccess(response.data.success)
                            setMessage(response.data.message)
                            setModal(!modal);
                        })
                        .catch(error => {
                            console.log(error);
                        });
                }
            })
            .catch(error => {
                console.log(error);
            });
    }

    const closeModal = () => {
        setModal(!modal);
        window.location.reload(false)
    }

    if (modal) {
        document.body.classList.add('active-modal')
    } else {
        document.body.classList.remove('active-modal')
    }

    return (
        <>
            <button type="button" className='theme-btn-one btn-black-overlay btn_sm' onClick={() => handleAddToCart(productId)}>{nameBtn}</button>

            {modal && (
                <div className="modal">
                    <div onClick={closeModal} className="overlay"></div>
                    <div className="modal-content">
                        <div>
                            {success === true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                        </div>
                        <h2 className="title_modal">{nameBtn} {success ? 'Successfully' : 'Failed'}</h2>
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

export default ModalNotifyAdd