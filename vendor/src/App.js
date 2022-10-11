import React from "react";
import {
  BrowserRouter,
  Routes,
  Route,
} from "react-router-dom";
import GlobalStyles from "./Components/GlobalStyles";
import './App.css';
import Vendor from "./pages/Vendor";

function App() {
  return (
    <GlobalStyles>
      <BrowserRouter>
        <Routes>
          <Route path="/*" element={<Vendor />}>
          </Route>
        </Routes>
      </BrowserRouter>
    </GlobalStyles>
  );
}

export default App;
