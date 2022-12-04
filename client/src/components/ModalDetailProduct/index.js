import styles from '../HotProduct/ProductWrapper/ProductWrapper.module.css'
import { FaExpand, FaRegHeart, FaRegCheckCircle, FaTimesCircle } from "react-icons/fa";
import { useEffect, useState } from 'react';
import axios from 'axios';

function ModalDetailProduct({productId}) {

    const [modal, setModal] = useState(false);
    const [message, setMessage] = useState('');
    const [success, setSuccess] = useState('');

    useEffect(() => {
        axios.get()
    }, [])
    
    const toggleModal = () => {
        setModal(!modal);
        
    }

    const closeModal = () => {
        setModal(!modal);
    }

    return (
        <>
            <a className={`${styles.action}`} title="Quickview" onClick={toggleModal}>
                <FaExpand />
            </a>

            {modal && (
                <div className="modal">
                    <div onClick={closeModal} className="overlay"></div>
                    <div className="modal-content">
                        <div>
                            
                        </div>
                        <h2 className="title_modal">Bạn chắc chắn muốn xóa sản phẩm?</h2>
                        <div className='divClose'>
                            <button className="close close-modal btnNo" onClick={closeModal}>Không</button>
                            <button className="close close-modal btnYes">Có</button>
                        </div>
                    </div>
                </div>
            )}
        </>
    )
}

export default ModalDetailProduct