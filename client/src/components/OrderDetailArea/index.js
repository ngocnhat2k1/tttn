import styles from './OrderDetailArea.module.css'
import { FaArrowLeft } from 'react-icons/fa'
import { useEffect, useState } from 'react'
import axios from 'axios'
import Cookies from 'js-cookie'
import { useParams } from "react-router-dom"
import { FaMinus, FaPlus, FaHeart, FaTimesCircle, FaRegCheckCircle } from "react-icons/fa"

function OrderDetailArea() {

    const { id } = useParams()
    const [order, setOrder] = useState('')
    const [check, setCheck] = useState(0);

    useEffect(() => {
        axios
            .get(`http://127.0.0.1:8000/api/user/order/${id}`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then(response => {
                if (response.data.success) {
                    setOrder(response.data.data);
                    setCheck(1);
                }
            })
            .catch(error => {
                console.log(error);
            });
    }, [])

    return (
        <>{check > 0 && 
            <div className={styles.detailArea}>
                <div className={styles.backBtn}>
                    <button type="button" className='theme-btn-one btn-black-overlay btn_sm'>
                        <FaArrowLeft size={12} /> TRỞ VỀ
                    </button>
                </div>
                <table align='center' border={0} cellPadding={0} cellSpacing={0} className={styles.boxTable}>
                    <tbody>
                        <tr>
                            <td>
                                <table align='center' border={0} cellPadding={0} cellSpacing={0}>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <img src="/Logo.png" alt="Logo" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div className={order.deleted_by == null ? 'colorSuccess' : 'colorFail'}>
                                                    {order.deleted_by == null ? <FaRegCheckCircle size={70} /> : <FaTimesCircle size={70} />}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h2 className={styles.title}>{order.deleted_by === 1 ? "XIN LỖI" : "CẢM ƠN"}</h2>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p>{order.order.deleted_by === 0 ? "Bạn đã hủy đơn hàng" : order.order.deleted_by === 1 ? "Đơn hàng của bạn đã bị hủy. Liên hệ cửa hàng để biết thêm chi tiết!" : order.order.status === 0 ? "Đơn hàng của bạn sẽ sớm được xử lý" : order.order.status === 1 ? "Đơn hàng của bạn đang được vận chuyển" : "Cảm ơn bạn đã mua hàng tại Shop Hướng Dương"}</p>
                                                <p>Mã đơn hàng: {order.order.idDelivery}</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table border={0} cellPadding={0} cellSpacing={0} className="mt-4">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <h2 className={styles.title}>CHI TIẾT ĐƠN HÀNG CỦA BẠN</h2>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table className={styles.orderDetail} border={0} cellPadding={0} cellSpacing={0} align='left'>
                                    <tbody>
                                        <tr align='left'>
                                            <th>HÌNH ẢNH</th>
                                            <th>SẢN PHẨM</th>
                                            <th>SỐ LƯỢNG</th>
                                            <th>GIÁ TIỀN</th>
                                            <th>GIẢM GIÁ</th>
                                        </tr>
                                        {order.product.map((product) => {
                                            return (
                                                <tr>
                                                    <td>
                                                        <img src={product.img} alt="" />
                                                    </td>
                                                    <td>
                                                        <h5>{product.name}</h5>
                                                    </td>
                                                    <td>
                                                        <h5></h5>
                                                    </td>
                                                    <td>
                                                        <h5>{product.price}</h5>
                                                    </td>
                                                </tr>
                                            )
                                        })}
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        }
        </>
    )
}

export default OrderDetailArea