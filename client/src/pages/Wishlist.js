import { useEffect } from 'react';
import '../App.css';
import axios from 'axios';
import 'bootstrap/dist/css/bootstrap.min.css';
import CommonBanner from '../components/CommonBanner';
import WishlistArea from '../components/WishlistArea';
import Cookies from 'js-cookie'
import { useNavigate } from 'react-router-dom';

function Wishlist() {
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
      <CommonBanner namePage="Yêu thích" />
      <WishlistArea />
    </>
  )
};

export default Wishlist;