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
import AccountEditArea from "./Components/AccountEditArea";

function App() {
  return (
    <GlobalStyles>
      <BrowserRouter>
        <Routes>
          <Route path="/*" element={<Vendor />} />
          <Route path='/login' element={<Login />} />
          <Route path="/register" element={<Register />} />
          <Route path="/changeInformation" element={<AccountEditArea />} />
        </Routes>
      </BrowserRouter>
    </GlobalStyles>
  );
}

export default App;
