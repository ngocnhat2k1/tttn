import { useEffect } from 'react';
import '../App.css';
import Cookies from 'js-cookie';
import 'bootstrap/dist/css/bootstrap.min.css';
import CommonBanner from '../components/CommonBanner';
import MyAccountArea from '../components/MyAccountArea';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';

function Customer() {

  const navigate = useNavigate();

  useEffect(() => {
    axios
      .get(`http://localhost:8000/api/retrieveToken`, {
        headers: {
          Authorization: `Bearer ${Cookies.get('token')}`,
        },
      })
      .then((response) => {
        if (!response.data.success) {
          navigate('/login')
        }
      })
      .catch(function (error) {
        console.log(error);
      });
  }, []);

  return (
    <>
      <CommonBanner namePage="Trang cá nhân" />
      <MyAccountArea />
    </>
  )
};

export default Customer;