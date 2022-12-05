import { BrowserRouter, Routes, Route } from "react-router-dom";
import Home from "./pages/Home";
import Contact from "./pages/Contact";
import Login from "./pages/Login";
import Register from "./pages/Register";
import Shop from "./pages/Shop";
import Customer from "./pages/Customer";
import Cart from "./pages/Cart";
import GlobalStyles from "./components/GlobalStyles";
import OrderComplete from "./pages/OrderComplete";
import Header from "./components/Header";
import Footer from "./components/Footer";
import AccountEdit from "./pages/AccountEdit";
import Wishlist from "./pages/Wishlist";
import AddressEdit from "./pages/AddressEdit";
import NotFound from "./components/NotFound";
import "./components/ModalATag/Modal.css"
import AddressCreate from "./pages/AddressCreate";
import DetailProduct from "./components/ShopMainArea/DetailProduct/DetailProduct";
import OrderDetail from "./pages/OrderDetail";
import CheckoutOrderPage from "./pages/CheckoutOrderPage";
import OrderCompleteArea from "./components/OrderCompleteArea";

function App() {

  return (
    <GlobalStyles>
      <BrowserRouter>
        <div className="App">
          <Header />
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/shop" element={<Shop />} />
            <Route path="/shop/:productId" element={<DetailProduct />} />
            <Route path="/login" element={<Login />} />
            <Route path="/register" element={<Register />} />
            <Route path="/my-account/*" element={<Customer />} />
            <Route path="/contact" element={<Contact />} />
            <Route path="/cart" element={<Cart />} />
            <Route path="/wishlist" element={<Wishlist />} />
            <Route path="/order-complete" element={<OrderComplete />} />
            <Route path="/account-edit" element={<AccountEdit />} />
            <Route path="/address-create" element={<AddressCreate />} />
            <Route path="/address-edit" element={<NotFound />} />
            <Route path="/address-edit/:id" element={<AddressEdit />} />
            <Route path="/checkout-order/" element={<CheckoutOrderPage />} />
            <Route path="/order-completed" element={<OrderCompleteArea />} />
            <Route path="*" element={<NotFound />}>
            </Route>
          </Routes>
          <Footer />
        </div>
      </BrowserRouter>
    </GlobalStyles>
  )
};

export default App;