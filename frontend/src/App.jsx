import "./App.css";
// import { Route, Switch } from "react-router";
// import { Router } from 'react-router-dom';
import { LoginPage } from "./pages/LoginPage";
// import { ContactListPage } from "./pages/ContactListPage";

function App() {
  // return (
  //   <Router>
  //     <div>
  //       <Switch>
  //         <Route exact path="/" component={LoginPage} />
  //         <Route path="/contactList" component={ContactListPage} />
  //       </Switch>
  //     </div>
  //   </Router>
  // );

  return <LoginPage/>;
}

export default App;
