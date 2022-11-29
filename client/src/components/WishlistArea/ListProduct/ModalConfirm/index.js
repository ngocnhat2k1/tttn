import React, { useState } from 'react';
import { FaQuestionCircle, FaRegCheckCircle, FaTimesCircle } from 'react-icons/fa';
import axios from 'axios';
import Cookies from 'js-cookie';

const ModalConfirm = ({ productId, icon }) => {
    const [modal, setModal] = useState(false);
    const [modal2, setModal2] = useState(false);
    const [message, setMessage] = useState('');
    const [success, setSuccess] = useState('');

    const handleDeleteProduct = (productId) => {
        axios
            .delete(`http://localhost:8000/api/user/favorite/destroy/${productId}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then(response => {
                setModal(!modal);
                setSuccess(response.data.success)
                setMessage(response.data.message)
                setModal2(!modal2);
            })
            .catch(error => {
                console.log(error);
            });
    }

    const toggleModal = () => {
        setModal(!modal);
    }

    const closeModal = () => {
        setModal(!modal);
    }

    const closeModal2 = () => {
        setModal2(!modal2);
        if (success) {
            window.location.reload(false)
        }
    }

    if (modal) {
        document.body.classList.add('active-modal')
    } else {
        document.body.classList.remove('active-modal')
    }

    if (modal2) {
        document.body.classList.add('active-modal')
    } else {
        document.body.classList.remove('active-modal')
    }

    return (
        <>
            <button className='btnSvg' onClick={toggleModal} >{icon}</button>

            {modal && (
                <div className="modal">
                    <div onClick={closeModal} className="overlay"></div>
                    <div className="modal-content">
                        <div>
                            <FaQuestionCircle className='svgQuestion' size={90} />
                        </div>
                        <h2 className="title_modal">Bạn chắc chắn muốn xóa sản phẩm?</h2>
                        <div className='divClose'>
                            <button className="close close-modal btnNo" onClick={closeModal}>Không</button>
                            <button className="close close-modal btnYes" onClick={() => handleDeleteProduct(productId)}>Có</button>
                        </div>
                    </div>
                </div>
            )}

            {modal2 && (
                <div className="modal">
                    <div onClick={closeModal2} className="overlay"></div>
                    <div className="modal-content">
                        <div>
                            {success === true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                        </div>
                        <h2 className="title_modal">Xóa sản phẩm {success ? "thành công" : "thất bại"}</h2>
                        <p className='p_modal'>{message}</p>
                        <div className='divClose'>
                            <button className="close close-modal" onClick={closeModal2}>OK</button>
                        </div>
                    </div>
                </div>
            )}
        </>
    )
}

export default ModalConfirm