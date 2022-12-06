import React, { useState, useRef } from 'react'
import { FaBars, FaTimes } from "react-icons/fa"
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import Container from 'react-bootstrap/Container';
import LogoSrc from '../../../images/Logo.png'
import { HomeDropDown, FeatureDropDown, ShopDropDown, BlogDropDown, PagesDropDown } from './NavBarItems.js'
import "./NavBar.css";
import { Link, } from 'react-router-dom';
import { FaHeart, FaShoppingBag, FaSearch } from "react-icons/fa";

function NavBar() {
  const navRef = useRef();

  const ShowNavBar = () => {
    navRef.current.classList.toggle("responsive_nav")
  }
  return (
    < nav className='NavBar' >
      <Container>
        <Row>
          <Col lg={12} className='d-flex align-items-center justify-content-between'>
            <div className='header-logo'>
              <div className='logo'>
                <Link to="/">
                  < img src={LogoSrc} alt="" className="Logo" />
                </Link>
              </div>
            </div>
            <ul className='nav-item-ul'
              ref={navRef}>
              <li
                className='nav-item'
              >
                <Link to='./'>Trang Chủ</Link>
              </li>
              <li
                className='nav-item' >
                <Link to='/Shop'>Cửa Hàng</Link>
              </li>

              <li className='nav-item' >
                <Link to='/contact'>Liên Hệ</Link>
              </li>
              <li className='nav-item' >
                <Link to='./my-account'>Tổng Quan</Link>
              </li>
              <button
                className='nav-btn nav-close-btn'
                onClick={ShowNavBar}
              >
                <FaTimes />
              </button>
            </ul>
            <ul className="ActionNavBar">
              <li> <Link to='/wishlist'><FaHeart fontSize={21} /></Link></li>
              <li> <Link to='/cart'><FaShoppingBag fontSize={21} /></Link></li>
              {/* <li> <Link to={ }><FaSearch fontSize={21} /></Link></li> */}
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