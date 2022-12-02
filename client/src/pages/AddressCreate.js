import { useEffect } from 'react';
import '../App.css';
import Cookies from 'js-cookie';
import 'bootstrap/dist/css/bootstrap.min.css';
import CommonBanner from '../components/CommonBanner';
import CreateAddressArea from '../components/CreateAddressArea';
import axios from 'axios';

function AddressCreate() {

    useEffect(() => {
        if (Cookies.get('token')) {
            axios
                .get(`http://localhost:8000/api/retrieveToken`, {
                    headers: {
                        Authorization: `Bearer ${Cookies.get('token')}`,
                    },
                })
                .then((response) => {
                    if (!response.data.success) {
                        window.location.href = 'http://localhost:3000/login';
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        } else {
            window.location.href = 'http://localhost:3000/login';
        }
    }, []);

    return (
        <>
            <CommonBanner namePage="Thêm địa chỉ" />
            <CreateAddressArea />
        </>
    )
};

export default AddressCreate;