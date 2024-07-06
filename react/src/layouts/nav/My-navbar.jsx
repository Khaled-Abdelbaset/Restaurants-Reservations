import { useContext, useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import {
  Navbar,
  Nav,
  Form,
  FormControl,
  Button,
  Image,
  InputGroup,
} from "react-bootstrap";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faSun,
  faMoon,
  faBars,
  faSearch,
} from "@fortawesome/free-solid-svg-icons";
import { BodyColorContext } from "../../BodyColorContext";
import { NavLink } from "react-router-dom";
import { decryptData } from "../../helpers/cryptoUtils";
import { logout } from "../../slices/auth/authSlice";

import "./navbar-style.css";

export default function MyNavbar() {
  const dispatch = useDispatch();
  const isAuth = useSelector((state) => state.auth.loggedIn);
  const { bodyColor, toggleColor } = useContext(BodyColorContext);
  const [searchFocused, setSearchFocused] = useState(false);
  const [isLoggedIn, setIsLoggedIn] = useState(false);

  useEffect(() => {
    decryptData("token") ? setIsLoggedIn(true) : setIsLoggedIn(false);
  }, [isAuth]);

  const handleLogout = () => {
    dispatch(logout());
    setIsLoggedIn(false);
  };

  return (
    <Navbar bg={bodyColor} expand="lg" className="sticky-top general">
      <Navbar.Brand href="" className="d-flex align-items-center">
        <Image
          src="./images/logo-white.png"
          roundedCircle
          alt="Logo"
          className="mx-5"
          style={{ width: "4rem", height: "4rem", borderRadius: "50%" }}
        />
      </Navbar.Brand>

      <Navbar.Toggle aria-controls="basic-navbar-nav">
        <FontAwesomeIcon icon={faBars} />
      </Navbar.Toggle>

      <Navbar.Collapse id="basic-navbar-nav">
        <div className="nav-cover">
          <Form
            className={
              searchFocused ? "focused search-bar" : "not-focused search-bar"
            }
          >
            <InputGroup>
              <InputGroup.Text id="search-icon">
                <FontAwesomeIcon icon={faSearch} />
              </InputGroup.Text>
              <FormControl
                type="text"
                placeholder="Search"
                aria-label="Search"
                className="search"
                aria-describedby="search-icon"
                onFocus={() => setSearchFocused(true)}
                onBlur={() => setSearchFocused(false)}
              />
              <Button
                variant={`outline-${
                  bodyColor === "light" ? "primary" : "yello-color"
                }`}
              >
                Search
              </Button>
            </InputGroup>
          </Form>
        </div>
        <Nav className="ml-auto nav-elements">
          <NavLink
            className={({ isActive }) =>
              isActive
                ? `active text-${bodyColor === "light" ? "day" : "night"}`
                : `text-${bodyColor === "light" ? "day" : "night"}`
            }
            to="/"
          >
            Home
          </NavLink>
          {!isLoggedIn && (
            <>
              <NavLink
                className={({ isActive }) =>
                  isActive
                    ? `active text-${bodyColor === "light" ? "day" : "night"}`
                    : `text-${bodyColor === "light" ? "day" : "night"}`
                }
                to="/about-us"
              >
                About
              </NavLink>

              <NavLink
                className={({ isActive }) =>
                  isActive
                    ? `active text-${bodyColor === "light" ? "day" : "night"}`
                    : `text-${bodyColor === "light" ? "day" : "night"}`
                }
                to="/login"
              >
                Login
              </NavLink>
            </>
          )}
          {isLoggedIn && (
            <>
              <NavLink
                className={({ isActive }) =>
                  isActive
                    ? `active text-${bodyColor === "light" ? "day" : "night"}`
                    : `text-${bodyColor === "light" ? "day" : "night"}`
                }
                to="/userprofile"
              >
                Profile
              </NavLink>
              <NavLink
                className={({ isActive }) =>
                  isActive
                    ? `active text-${bodyColor === "light" ? "day" : "night"}`
                    : `text-${bodyColor === "light" ? "day" : "night"}`
                }
                onClick={handleLogout}
                to={"/"}
              >
                Logout
              </NavLink>
            </>
          )}
          <Button
            variant={`outline-${
              bodyColor === "light" ? "secondary" : "warning"
            } bg-${
              bodyColor === "light" ? "light" : "dark"
            } rounded-pill button-width`}
            onClick={toggleColor}
          >
            {bodyColor === "light" ? (
              <FontAwesomeIcon
                icon={faMoon}
                className="p-0 fs-5 font-weight-light"
              />
            ) : (
              <FontAwesomeIcon
                icon={faSun}
                className="p-0 sun-large-screen fs-5 font-weight-light text-warning"
              />
            )}
          </Button>
        </Nav>
      </Navbar.Collapse>
    </Navbar>
  );
}
