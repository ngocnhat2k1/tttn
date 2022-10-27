import '../App.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import FacebookInfo from '../components/FacebookInfo';
import ProductIntroduction from '../components/ProductIntroduction';
import OfferCountdown from '../components/OfferCountdown'
import Banner from '../components/Banner'
import HotProduct from '../components/HotProduct';
import TrendingIntroduction from '../components/TrendingIntroduction';

function Home() {
  return (
    <>
      <Banner />
      <ProductIntroduction />
      <HotProduct />
      <OfferCountdown />
      <TrendingIntroduction />
      <FacebookInfo />
    </>
  )
};

export default Home;