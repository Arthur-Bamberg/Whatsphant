import {
  createBrowserRouter,
  RouterProvider,
} from "react-router-dom";
import "./App.css";
import { LoginPage } from "./pages/LoginPage";
import { ContactListPage } from "./pages/ContactListPage";

function App() {
  const router = createBrowserRouter([
    {
      path: "/",
      element: <LoginPage />,
    },
    {
      path: "/contacts",
      element: <ContactListPage />
    }
  ]);
  
  return <RouterProvider router={router} />
}

export default App;
