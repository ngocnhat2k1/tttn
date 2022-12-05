import React, { useState } from "react";
import { Link } from "react-router-dom";
import "./Dropdown.css";

function Dropdown(props) {
    const [dropdown, setDropdown] = useState(false);
    const nameNavbar = props.nameDropDown
    return (
        <>
            <ul
                className={`${dropdown ? "services-submenu clicked" : "services-submenu sub-menu"} ${props.className}`}
                onClick={() => setDropdown(!dropdown)}
            >
                {nameNavbar.map((item) => {
                    return (
                        <li key={item.id} className='has-dropdown'>
                            <Link
                                to={item.path}
                                className={item.cName}
                                onClick={() => setDropdown(false)}
                            >
                                {item.title}
                            </Link>
                        </li>
                    );
                })}
            </ul>
        </>
    );
}

export default Dropdown;
