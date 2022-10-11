import React, { useState } from 'react'
import { Link } from "react-router-dom"
import { FaTachometerAlt, FaShoppingCart, FaShoppingBag, FaRegIdBadge, FaUser, FaCog } from "react-icons/fa"
import "./TabList.css"


function TabList() {

    const [isActive, setActive] = useState("dashboard")

    return (
        <>
            <div className='dashboard_tab_button'>
                <ul className='nav flex-column dashboard'>
                    <li >
                        <Link
                            onClick={() => setActive('dashboard')}
                            className={`${isActive === "dashboard" ? 'active_tablist' : ' '} `}
                            to="/"> <i> <FaTachometerAlt color='black' /></i> dashboard </Link>
                    </li>
                    <li>
                        <Link
                            onClick={() => setActive('allproduct')}
                            className={`${isActive === "allproduct" ? 'active_tablist' : ' '} `}
                            to="/all-product">  <i> <FaShoppingCart color='black' /></i> product </Link>
                    </li>
                    <li>
                        <Link
                            onClick={() => setActive('allorder')}
                            className={`${isActive === "allorder" ? 'active_tablist' : ' '} `}
                            to="/all-order">  <i> <FaShoppingBag color='black' /></i> order </Link>
                    </li>
                    <li
                    >
                        <Link
                            onClick={() => setActive('profile')}
                            className={`${isActive === "profile" ? 'active_tablist' : ' '} `}
                            to="/vendor-profile">  <i> <FaRegIdBadge color='black' /></i> profile </Link>
                    </li>
                    <li>
                        <Link
                            onClick={() => setActive('addproduct')}
                            className={`${isActive === "addproduct" ? 'active_tablist' : ' '} `}
                            to="/add-products">  <i> <FaUser color='black' /></i> add product </Link>
                    </li>
                    <li
                    >
                        <Link
                            onClick={() => setActive('setting')}
                            className={`${isActive === "setting" ? 'active_tablist' : ' '} `}
                            to="/vendor-setting">  <i> <FaCog color='black' /></i> setting </Link>
                    </li>
                </ul>
            </div>
        </>
    )
}

export default TabList