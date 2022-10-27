import { useEffect, useState } from 'react';
import '../App.css';
import Cookies from 'js-cookie';
import 'bootstrap/dist/css/bootstrap.min.css';
import CommonBanner from '../components/CommonBanner';
import AddressEditArea from '../components/AddressEditArea';
import axios from 'axios';
import { Link, Route, Routes } from 'react-router-dom'

function AddressEdit() {

    const [listAddress, setListAddress] = useState([]);

    // useEffect(() => {
    //     axios
    //         .get(`http://localhost:8000/api/retrieveToken`, {
    //             headers: {
    //                 Authorization: `Bearer ${Cookies.get('token')}`,
    //             },
    //         })
    //         .then((response) => {
    //             if (!response.data.success) {
    //                 window.location.href = 'http://localhost:3000/login';
    //             }
    //         })
    //         .catch(function (error) {
    //             console.log(error);
    //         });
    // }, []);

    useEffect(() => {
        axios
            .get(`http://localhost:8000/api/user/address`, {
                headers: {
                    Authorization: `Bearer ${Cookies.get('token')}`,
                },
            })
            .then((response) => {
                if (response.data.success) {
                    setListAddress(response.data.data);
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }, []);

    return (
        <>
            <CommonBanner namePage="Address Info Edit" />
            <Routes>
                {listAddress.leng !== 0 &&
                    listAddress.map((address, index) => {
                        return (
                            <Route key={index} path={`/id=${address.id}`} element={<AddressEditArea id={address.id} stt={index}/>}></Route>
                        )
                    })}
            </Routes>
        </>
    )
};

export default AddressEdit;