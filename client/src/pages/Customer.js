import { useEffect } from 'react';
import '../App.css';
import Cookies from 'js-cookie';
import 'bootstrap/dist/css/bootstrap.min.css';
import CommonBanner from '../components/CommonBanner';
import MyAccountArea from '../components/MyAccountArea';
import axios from 'axios';

function Customer() {

  useEffect(() => {
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
  }, []);

  return (
    <>
      <CommonBanner namePage="Customer Dashboard" />
      <MyAccountArea />
    </>
  )
};

export default Customer;