import { useEffect } from 'react';
import '../App.css';
import axios from 'axios';
import CommonBanner from '../components/CommonBanner';
import RegisterArea from '../components/RegisterArea';
import Cookies from 'js-cookie'

function Register() {
  useEffect(() => {
    axios
      .get(`http://localhost:8000/api/retrieveToken`, {
        headers: {
          Authorization: `Bearer ${Cookies.get('token')}`,
        },
      })
      .then((response) => {
        if (response.data.success) {
          window.location.href = 'http://localhost:3000/my-account';
        }
      })
      .catch(function (error) {
        console.log(error);
      });
  }, []);

    return (
        <>
          <CommonBanner namePage="Register"/>
          <RegisterArea />
        </>
    )
};

export default Register;