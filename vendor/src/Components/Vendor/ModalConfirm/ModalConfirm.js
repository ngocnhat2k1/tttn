import React, { useState } from 'react'
import { FaRegCheckCircle, FaTimesCircle, FaTimes } from 'react-icons/fa'


const ModalConfirm = ({ success, message }) => {
    const [isTrue, setIsTrue] = useState(true)

    const closeNotify = () => {
        if (success) {
            window.location.reload(false)
        } else {
            setIsTrue(false)
        }
    }

    return (
        <>{isTrue &&
            <div className="modal text-center" >
                <div onClick={closeNotify} className="overlay"></div>
                <div className="modal-content">
                    <div>
                        {success == true ? <FaRegCheckCircle size={90} className='colorSuccess' /> : <FaTimesCircle size={90} className='colorFail' />}
                    </div>
                    <h2 className="title_modal">{success ? 'Thành công' : 'Không thành công'}</h2>
                    <p className='p_modal'>{message}</p>
                    <div className="btn_right_table">
                        <button className="theme-btn-one bg-black btn_sm" onClick={closeNotify}>Đóng </button>
                    </div>
                    <div className='divClose'>
                        <button className="close close-modal" onClick={closeNotify}><FaTimes /></button>
                    </div>
                </div>
            </div>
        }
        </>
    )
}

export default ModalConfirm