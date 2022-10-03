import {
  BrowserRouter,
  Routes,
  Route,
} from "react-router-dom";
import GlobalStyles from "./components/GlobalStyles";
import './App.css';

function App() {
  return (
    <GlobalStyles>
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<Home />}>
          </Route>
        </Routes>
      </BrowserRouter>
    </GlobalStyles>
  );
}

export default App;
