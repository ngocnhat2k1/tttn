import { Component } from "react";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import Home from "./pages/Home";
import Contact from "./pages/Contact";
import Login from "./pages/Login";
import Register from "./pages/Register";
import Shop from "./pages/Shop";
import Customer from "./pages/Customer";
import Cart from "./pages/Cart";
import GlobalStyles from "./components/GlobalStyles";

class App extends Component {
  render() {
    return (
        <GlobalStyles>
          <BrowserRouter>
            <Routes>
              <Route path="/" element={<Home />}>
              </Route>
              <Route path="/shop" element={<Shop />}>
              </Route>
              <Route path="/login" element={<Login />}>
              </Route>
              <Route path="/register" element={<Register />}>
              </Route>
              <Route path="/my-account/*" element={<Customer />}>
              </Route>
              <Route path="/contact" element={<Contact />}>
              </Route>
              <Route path="/cart" element={<Cart />}>
              </Route>
            </Routes>
          </BrowserRouter>
        </GlobalStyles>
    )
  };
};

export default App;