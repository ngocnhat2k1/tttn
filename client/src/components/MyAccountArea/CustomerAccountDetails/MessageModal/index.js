import { useState, useEffect } from 'react';
import "../../../ModalATag/Modal.css"
import { FaRegCheckCircle, FaTimesCircle } from 'react-icons/fa'
import axios from 'axios'
import Cookies from 'js-cookie';

function MessageModal({ subsc }) {

    const [modal, setModal] = useState(false);
    const [subscribe, setSubscribe] = useState('');
    const [message, setMessage] = useState('');
    const [success, setSuccess] = useState('');

    useEffect(() => {
        setSubscribe(subsc)
    }, [subsc])

    const handleSubcribe = () => {
        // axios
        //     .get(`http://localhost:8000/customer/subscribe`, {
        //         headers: {
        //             Authorization: `Bearer ${Cookies.get('token')}`,
        //         },
        //     })
        //     .then(response => {
        //         if (response.data.success) {
        //             setSubscribe(!subscribe);
        //             setSuccess(response.data.success);
        //             setMessage(response.data.message);
        //             setModal(!modal)
        //         } else {
        //             setSubscribe(!subscribe);
        //             setSuccess(response.data.success);
        //             setMessage(response.data.message);
        //             setModal(!modal)
        //         }
        //     })
        //     .catch(err => {
        //         console.log(err);
        //     })
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
            {/* <button type="submit" className="theme-btn-one bg-black btn_sm" onClick={toggleModal}>{nameBtn}</button> */}
            <label className="mt-4" htmlFor="newsletter">
                <input type="checkbox" id="newsletter" onChange={handleSubcribe} checked={subscribe === true ? true : false} />
                <span className="ml-2">{subscribe ? 'Unsubscribe for our newsletter' : 'Sign up for our newsletter'}</span>
                <p className="mt-2 text-secondary">{subscribe ? 'You can miss our promotions.' : 'You may unsubscribe at any moment. Please subscribe us to receive the earliest promotion notifications.'}</p>
            </label>

            {modal && (
                <div className="modal">
                    <div onClick={closeModal} className="overlay"></div>
                    <div className="modal-content">
                        <div>
                            {success == true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                        </div>
                        <h2 className="title_modal">{subscribe ? 'Subscribe' : 'Unsubscribe'} {success ? 'Successfully' : 'Failed'}</h2>
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

export default MessageModal