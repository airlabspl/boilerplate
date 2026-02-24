import { BrowserRouter, Route, Routes } from "react-router";
import Home from "./page/home";
import Dashboard from "./page/dashboard";

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route index element={<Home />} />
        <Route path="dashboard" element={<Dashboard />} />
      </Routes>
    </BrowserRouter>
  )
}

export default App;
