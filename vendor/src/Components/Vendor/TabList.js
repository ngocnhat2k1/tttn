import React, { useState } from 'react'
import { Link } from "react-router-dom"
import { FaTachometerAlt, FaShoppingCart, FaShoppingBag, FaRegIdBadge, FaUser } from "react-icons/fa"
import "./TabList.css"


function TabList() {
    const duongdan = window.location.pathname
    const [isActive, setActive] = useState(duongdan)
    console.log(duongdan)

    return (
        <>
            <div className='dashboard_tab_button'>
                <ul className='nav flex-column dashboard'>
                    <li >
                        <Link
                            onClick={() => setActive('/')}
                            className={`${isActive === "/" ? 'active_tablist' : ' '} `}
                            to="/"> <i> <FaTachometerAlt color='black' /></i> dashboard </Link>
                    </li>
                    <li>
                        <Link
                            onClick={() => setActive('/all-product')}
                            className={`${isActive === "/all-product" ? 'active_tablist' : ' '} `}
                            to="/all-product">  <i> <FaShoppingCart color='black' /></i> product </Link>
                    </li>
                    <li>
                        <Link
                            onClick={() => setActive('/all-order')}
                            className={`${isActive === "/all-order" ? 'active_tablist' : ' '} `}
                            to="/all-order">  <i> <FaShoppingBag color='black' /></i> order </Link>
                    </li>
                    <li
                    >
                        <Link
                            onClick={() => setActive('/vendor-profile')}
                            className={`${isActive === "/vendor-profile" ? 'active_tablist' : ' '} `}
                            to="/vendor-profile">  <i> <FaRegIdBadge color='black' /></i> profile </Link>
                    </li>
                    <li>
                        <Link
                            onClick={() => setActive('/add-product')}
                            className={`${isActive === "/add-product" ? 'active_tablist' : ' '} `}
                            to="/add-products">  <i> <FaUser color='black' /></i> add product </Link>
                    </li>
                </ul>
            </div>
        </>
    )
}

export default TabList