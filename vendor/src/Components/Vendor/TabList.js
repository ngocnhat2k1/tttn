import React, { useState } from 'react'
import { Link } from "react-router-dom"
import { FaTachometerAlt, FaShoppingCart, FaShoppingBag, FaUser, FaListAlt, FaGift, FaCommentAlt } from "react-icons/fa"
import "./TabList.css"


function TabList() {
    const duongdan = window.location.pathname
    const [isActive, setActive] = useState(duongdan)

    return (
        <>
            <div className='dashboard_tab_button'>
                <ul className='nav flex-column dashboard'>
                    <li >
                        <Link
                            onClick={() => setActive('/')}
                            className={`${isActive === "/" ? 'active_tablist' : ' '} `}
                            to="/"> <i> <FaTachometerAlt color='black' /></i> Tổng Quan </Link>
                    </li>
                    <li>
                        <Link
                            onClick={() => setActive('/all-product')}
                            className={`${isActive === "/all-product" ? 'active_tablist' : isActive === "/add-product" ? 'acitve_tablist' : ' '} `}
                            to="/all-product">  <i> <FaShoppingCart color='black' /></i> Danh Sách Sản Phẩm </Link>
                    </li>
                    <li>
                        <Link
                            onClick={() => setActive('/all-order')}
                            className={`${isActive === "/all-order" ? 'active_tablist' : ' '} `}
                            to="/all-order">  <i> <FaShoppingBag color='black' /></i> Danh Sách Đơn Hàng </Link>
                    </li>
                    <li
                    >
                        <Link
                            onClick={() => setActive('/vendor-users')}
                            className={`${isActive === "/vendor-users" ? 'active_tablist' : ' '} `}
                            to="/vendor-users">  <i> <FaUser color='black' /></i> Danh Sách Người Dùng </Link>
                    </li>
                    <li
                    >
                        <Link
                            onClick={() => setActive('/vendor-category')}
                            className={`${isActive === "/vendor-category" ? 'active_tablist' : isActive === "/add-category" ? 'active_tablist' : ''} `}
                            to="/vendor-category">  <i> <FaListAlt color='black' /></i> Danh Mục </Link>
                    </li>
                    <li
                    >
                        <Link
                            onClick={() => setActive('/vendor-voucher')}
                            className={`${isActive === "/vendor-voucher" ? 'active_tablist' : isActive === "/add-voucher" ? 'active_tablist' : ''} `}
                            to="/vendor-voucher">  <i> <FaGift color='black' /></i> Mã Giảm Giá </Link>
                    </li>
                    <li>
                        <Link
                            onClick={() => setActive('/feedback')}
                            className={`${isActive === "/feedback" ? 'active_tablist' : ' '} `}
                            to="/feedback">  <i> <FaCommentAlt color='black' /></i> Bình Luận  </Link>
                    </li>
                </ul>
            </div>
        </>
    )
}

export default TabList