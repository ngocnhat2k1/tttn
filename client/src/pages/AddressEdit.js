import { useEffect} from 'react';
import '../App.css';
import Cookies from 'js-cookie';
import 'bootstrap/dist/css/bootstrap.min.css';
import CommonBanner from '../components/CommonBanner';
import AddressEditArea from '../components/AddressEditArea';
import axios from 'axios';

function AddressEdit() {

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
            <CommonBanner namePage="Cập nhật địa chỉ" />
            <AddressEditArea />
        </>
    )
};

export default AddressEdit;