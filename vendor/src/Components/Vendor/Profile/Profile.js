import React from 'react'
import 'bootstrap/dist/css/bootstrap.min.css';
import Col from 'react-bootstrap/Col';
import Row from 'react-bootstrap/Row'
import '../DashBoard.css'
import './Profile.css'
import { FakeProfile } from '../FakeData/FakeProfile';

const Profile = () => {
    return (
        <Col sm={12} md={12} lg={9}>
            <div className='tab-content dashboard_content'>
                <div className='tab-pane fade show active'>
                    <div className='vendors_profiles'>
                        <h4>Profile</h4>
                        <ul>
                            <li>
                                <div className='profils_details_vendor'>
                                    <div className='profile_left'>
                                        <h4>Company Name:</h4>
                                    </div>
                                    <div className='profile_right'>
                                        <h4>Fashion Store</h4>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div className='profils_details_vendor'>
                                    <div className='profile_left'>
                                        <h4>Email Address:</h4>
                                    </div>
                                    <div className='profile_right'>
                                        <h4>demo123@gmail.com</h4>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div className='profils_details_vendor'>
                                    <div className='profile_left'>
                                        <h4>Country / Region:</h4>
                                    </div>
                                    <div className='profile_right'>
                                        <h4>Downers Grove, IL</h4>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div className='profils_details_vendor'>
                                    <div className='profile_left'>
                                        <h4>Country / Region:</h4>
                                    </div>
                                    <div className='profile_right'>
                                        <h4>Downers Grove, IL</h4>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div className='profils_details_vendor'>
                                    <div className='profile_left'>
                                        <h4>Year Established:</h4>
                                    </div>
                                    <div className='profile_right'>
                                        <h4>2018</h4>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div className='profils_details_vendor'>
                                    <div className='profile_left'>
                                        <h4>Total Employees:</h4>
                                    </div>
                                    <div className='profile_right'>
                                        <h4>101 - 200 People</h4>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div className='profils_details_vendor'>
                                    <div className='profile_left'>
                                        <h4>Category:</h4>
                                    </div>
                                    <div className='profile_right'>
                                        <h4>Clothing</h4>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div className='profils_details_vendor'>
                                    <div className='profile_left'>
                                        <h4>Street Address:</h4>
                                    </div>
                                    <div className='profile_right'>
                                        <h4>549 Sulphur Springs Road</h4>
                                    </div>
                                </div>
                            </li>

                        </ul>

                        <div className='btn_left_table'>
                            <a href="/account-edit" className='theme-btn-one bg-black btn-sm'>Edit Profile</a>

                        </div>
                    </div>

                </div>

            </div>

        </Col>
    )
}

export default Profile