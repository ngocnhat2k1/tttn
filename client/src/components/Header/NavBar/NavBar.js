import React, { useState, useRef } from 'react'
import { FaBars, FaTimes } from "react-icons/fa"
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import Container from 'react-bootstrap/Container';

import LogoSrc from '../../../images/Logo.png'
import { HomeDropDown, NavBarItems, FeatureDropDown, ShopDropDown, BlogDropDown, PagesDropDown } from './NavBarItems.js'
import "./NavBar.css";
import { Link, BrowserRouter as Router } from 'react-router-dom';
import { FaHeart, FaShoppingBag, FaSearch } from "react-icons/fa";
import DropDown from "./Dropdown.js";


function NavBar() {
  const [HomeDropdown, setHomeDropdown] = useState(false)
  const [ShopDropdown, setShopDropdown] = useState(false)
  const [FeatureDropdown, setFeatureDropdown] = useState(false)
  const [BlogDropdown, setBlogDropdown] = useState(false)
  const [PageDropdown, setPagesDropdown] = useState(false)

  const navRef = useRef();

  const ShowNavBar = () => {
    navRef.current.classList.toggle("responsive_nav")
    console.log("dã dính")
  }

  return (

    < nav className='NavBar' >
      <Container>
        <Row>
          <Col lg={12} className='d-flex align-items-center justify-content-between'>
            <div className='header-logo'>
              <div className='logo'>
                <a href=".">
                  < img src={LogoSrc} alt="" className="Logo" />
                </a>
              </div>
            </div>

            <div
              ref={navRef}
            >
              <ul className='nav-item-ul  '>
                {NavBarItems.map((item) => {
                  if (item.title === "Home") {
                    return (
                      <li
                        key={item.id}
                        className={item.cName}
                        onMouseEnter={() =>
                          setHomeDropdown(true)
                        }
                        onMouseLeave={() => setHomeDropdown(false)}
                      >
                        <Link to={item.path}

                        >{item.title}</Link>
                        <DropDown
                          className={`${HomeDropdown ? 'active' : 'dropdown'}`}
                          nameDropDown={HomeDropDown} />
                      </li>
                    );
                  }
                  else
                    if (item.title === "Shop") {
                      return (
                        <li
                          key={item.id}
                          className={item.cName}
                          onMouseEnter={() => setShopDropdown(true)}
                          onMouseLeave={() => setShopDropdown(false)}
                        >
                          <Link to={item.path}>{item.title}</Link>
                          <DropDown className={`${ShopDropdown ? 'active' : 'dropdown'}`}
                            nameDropDown={ShopDropDown} />
                        </li>
                      );
                    }
                    else
                      if (item.title === "Feature") {
                        return (
                          <li
                            key={item.id}
                            className={item.cName}
                            onMouseEnter={() => setFeatureDropdown(true)}
                            onMouseLeave={() => setFeatureDropdown(false)}
                          >
                            <Link to={item.path}>{item.title}</Link>
                            <DropDown className={`${FeatureDropdown ? 'active' : 'dropdown'}`}
                              nameDropDown={FeatureDropDown} />
                          </li>
                        )
                      }
                      else
                        if (item.title === "Blog") {
                          return (
                            <li
                              key={item.id}
                              className={item.cName}
                              onMouseEnter={() => setBlogDropdown(true)}
                              onMouseLeave={() => setBlogDropdown(false)}
                            >
                              <Link to={item.path}>{item.title}</Link>
                              <DropDown className={`${BlogDropdown ? 'active' : 'dropdown'}`}
                                nameDropDown={BlogDropDown} />
                            </li>
                          )
                        }
                        else
                          if (item.title === "Pages") {
                            return (
                              <li
                                key={item.id}
                                className={item.cName}
                                onMouseEnter={() => setPagesDropdown(true)}
                                onMouseLeave={() => setPagesDropdown(false)}
                              >
                                <Link to={item.path}>{item.title}</Link>
                                <DropDown className={`${PageDropdown ? 'active' : 'dropdown'}`}
                                  nameDropDown={PagesDropDown} />
                              </li>
                            )
                          }
                })}
                <button
                  className='nav-btn nav-close-btn'
                  onClick={ShowNavBar}
                >
                  <FaTimes />
                </button>
              </ul>


            </div>

            <ul className="ActionNavBar">
              <li> <a href="."><FaHeart fontSize={21} /></a></li>
              <li> <a href="."><FaShoppingBag fontSize={21} /></a></li>
              <li> <a href="."><FaSearch fontSize={21} /></a></li>
              <button
                className='nav-btn'
                onClick={ShowNavBar}
              >
                <FaBars fontSize={21} />
              </button>
            </ul>
          </Col>
        </Row>
      </Container>
    </nav >
  )
}


export default NavBar