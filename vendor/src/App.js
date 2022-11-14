import React from "react";
import {
  BrowserRouter,
  Routes,
  Route,
} from "react-router-dom";
import GlobalStyles from "./Components/GlobalStyles";
import './App.css';
import Vendor from "./pages/Vendor";
import Login from "./pages/Login";
import Register from "./pages/Register";

function App() {
  return (
    <GlobalStyles>
      <BrowserRouter>
        <Routes>
          <Route path="/*" element={<Vendor />} />
          <Route path='/login' element={<Login />} />
          <Route path="register" element={<Register />} />
        </Routes>
      </BrowserRouter>
    </GlobalStyles>
  );
}

export default App;
