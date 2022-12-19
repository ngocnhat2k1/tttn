import React, { useState } from "react";
import { FaTrash, FaTimes, FaRegCheckCircle, FaTimesCircle } from 'react-icons/fa'
import axios from 'axios';
import Cookies from 'js-cookie';
import { useForm } from "react-hook-form";
import "../../Modal.css";
import 'bootstrap/dist/css/bootstrap.min.css';
import ModalConfirm from "../../ModalConfirm/ModalConfirm";

const DeleteCategory = ({ idDetail, nameDetail }) => {
    const [modal, setModal] = useState(false);
    const [success, setSuccess] = useState("")
    const [message, setMessage] = useState('')
    const [notify, setNotify] = useState(false)
    const { handleSubmit, formState: { errors } } = useForm();
    const toggleModal = () => {
        setModal(!modal);
    };

    if (modal) {
        document.body.classList.add('active-modal')
    } else {
        document.body.classList.remove('active-modal')
    }
    const onSubmit = (data) => {
        setModal(!modal);
        axios
            .delete(`http://127.0.0.1:8000/api/v1/categories/${idDetail}/destroy`,
                {
                    headers: {
                        Authorization: `Bearer ${Cookies.get('adminToken')}`,
                    },
                },)
            .then((response) => {
                setSuccess(response.data.success)
                if (success) {
                    setMessage(response.data.message)
                } else {
                    setMessage(response.data.errors)
                }
                setNotify(true)

            })
            .catch(function (error) {
                alert(error);
                console.log(error);
            });
    }

    return (
        <>
            <FaTrash onClick={toggleModal} className="btn-modal"></FaTrash>
            {modal && (
                <div className="modal">
                    <div onClick={toggleModal} className="overlay"></div>
                    <div className="modal-content">
                        <h2 className="title_modal">Bạn muốn xoá danh mục  <p>{nameDetail}?</p></h2>
                        <form onSubmit={handleSubmit(onSubmit)}>
                            <div className="btn_right_table">
                                <button className="theme-btn-one bg-black btn_sm">Xoá </button>
                            </div>
                        </form>
                        <button className="close close-modal" onClick={toggleModal}><FaTimes /></button>
                    </div>
                </div>
            )}
            {notify && (
                <ModalConfirm message={message} success={success} />
            )}
        </>

    )
}

export default DeleteCategory