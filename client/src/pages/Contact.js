import '../App.css';
// import axios from 'axios';
import 'bootstrap/dist/css/bootstrap.min.css';
import CommonBanner from '../components/CommonBanner';
import ContactArea from '../components/ContactArea';

function Contact() {
  return (
    <>
      <CommonBanner namePage="Liên Hệ" />
      <ContactArea />
    </>
  )
};

export default Contact;