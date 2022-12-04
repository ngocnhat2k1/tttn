import { useState, useEffect } from 'react';
import "./Modal.css";
import { FaRegCheckCircle, FaTimesCircle } from 'react-icons/fa'

function AccountEditModal({ message, success, nameBtn }) {

    const [modal, setModal] = useState(false);

    const toggleModal = () => {
        setTimeout(() => { setModal(!modal); }, 1000)
    };

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
            <button type="submit" className="theme-btn-one bg-black btn_sm" onClick={toggleModal}>{nameBtn}</button>

            {modal && (
                <div className="modal">
                    <div onClick={toggleModal} className="overlay"></div>
                    <div className="modal-content">
                        <div>
                            {success == true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                        </div>
                        <h2 className="title_modal">{nameBtn} {success ? 'Successful' : 'Failed'}</h2>
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

export default AccountEditModal