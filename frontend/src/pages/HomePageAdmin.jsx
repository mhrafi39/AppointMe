import React, { useEffect, useState, useRef } from "react";
import { Link, useNavigate } from "react-router-dom";
import "../styles/HomePageAdmin.css";
import ServiceCard from "../components/ServiceCard";
import { API_BASE } from "../api";

const HomePageAdmin = () => {
  const [services, setServices] = useState([]);
  const [searchTerm, setSearchTerm] = useState("");
  const [pagination, setPagination] = useState({});
  const [dropdownOpen, setDropdownOpen] = useState(false);
  const [isAdmin, setIsAdmin] = useState(false); 
  const navigate = useNavigate();
  const dropdownRef = useRef();

  // Check admin token
  useEffect(() => {
    const adminToken = localStorage.getItem("admin_token");
    if (!adminToken) {
      navigate("/admin-login"); // redirect if not logged in as admin
    } else {
      setIsAdmin(true);
    }
  }, [navigate]);

  // Headers with JWT
  const getAuthHeaders = () => {
    const adminToken = localStorage.getItem("admin_token");
    return {
      "Content-Type": "application/json",
      Authorization: adminToken ? `Bearer ${adminToken}` : "",
    };
  };

  // Close dropdown when clicking outside
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
        setDropdownOpen(false);
      }
    };
    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, []);

  // Fetch services for admin
  const fetchServices = (url = `${API_BASE}/admin-services?per_page=15`) => {
    fetch(url, { headers: getAuthHeaders() })
      .then((res) => res.json())
      .then((data) => {
        setServices(data.data || []);
        setPagination({
          currentPage: data.current_page,
          lastPage: data.last_page,
          prevPageUrl: data.prev_page_url,
          nextPageUrl: data.next_page_url,
        });
      })
      .catch((err) => console.log(err));
  };

  useEffect(() => {
    if (isAdmin) fetchServices();
  }, [isAdmin]);

  // Search
  const handleSearch = (e) => {
    e.preventDefault();
    fetch(`${API_BASE}/search?search=${searchTerm}&per_page=15`, {
      headers: getAuthHeaders(),
    })
      .then((res) => res.json())
      .then((data) => {
        setServices(data.services?.data || []);
        setPagination({
          currentPage: data.services?.current_page,
          lastPage: data.services?.last_page,
          prevPageUrl: data.services?.prev_page_url,
          nextPageUrl: data.services?.next_page_url,
        });
      })
      .catch((err) => console.log(err));
  };

  // Pagination
  const handlePageClick = (url) => {
    if (url) fetchServices(url);
  };

  // Logout
  const handleLogout = () => {
    fetch(`${API_BASE}/admin/logout`, {
      method: "POST",
      headers: getAuthHeaders(),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          localStorage.removeItem("admin_token");
          navigate("/admin-login");
        } else console.error("Logout failed:", data.msg);
      })
      .catch((err) => console.error(err));
  };

  if (!isAdmin) return null; // do not render until admin confirmed

  return (
    <div className="admin-home-page">
      {/* Top bar */}
      <div className="top-bar">
        <form onSubmit={handleSearch} className="search-container">
          <input
            type="text"
            placeholder="Search with job name"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
          <button type="submit">Search</button>
        </form>

        <div className="actions" ref={dropdownRef}>
          <Link to="/service-form" className="create-service-btn">
            <span className="plus-icon">+</span> Create Service
          </Link>
          <div className="dropdown">
            <button
              className="dropdown-toggle"
              onClick={() => setDropdownOpen(!dropdownOpen)}
            >
              ☰ Menu
            </button>
            {dropdownOpen && (
              <div className="dropdown-menu">
                <Link to="/profile" className="dropdown-item">Profile</Link>
                <Link to="/bookings" className="dropdown-item">Bookings</Link>
                <Link to="/settings" className="dropdown-item">Settings</Link>
                <Link to="/admin-profile" className="dropdown-item">Profile</Link>
                <button onClick={handleLogout} className="dropdown-item logout">
                  Logout
                </button>
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Admin Navigation */}
      <nav className="admin-nav">
        <Link to="/admin-dashboard" className="nav-button">
          View Provider Applications
        </Link>
        <Link to="/admin-profile" className="nav-button">
          Admin Profile
        </Link>
      </nav>

      {/* Services Cards */}
      <div className="all-cards-container">
        {services.length === 0 ? (
          <p style={{ textAlign: "center", color: "#fff" }}>No services found.</p>
        ) : (
          services.map((service) => (
            <Link
              to={`/service/${service.services_id}`}
              key={service.services_id}
              className="service-card-link"
            >
              <ServiceCard service={service} />
            </Link>
          ))
        )}
      </div>

      {/* Pagination */}
      <div className="pagination-links">
        {pagination.prevPageUrl ? (
          <span onClick={() => handlePageClick(pagination.prevPageUrl)}>&lt; Previous</span>
        ) : (<span>&lt; Previous</span>)}

        {Array.from({ length: pagination.lastPage || 0 }, (_, i) => i + 1).map((page) => (
          <span
            key={page}
            className={page === pagination.currentPage ? "current" : ""}
            onClick={() => handlePageClick(`${API_BASE}/admin-services?per_page=15&page=${page}`)}
          >
            {page}
          </span>
        ))}

        {pagination.nextPageUrl ? (
          <span onClick={() => handlePageClick(pagination.nextPageUrl)}>Next &gt;</span>
        ) : (<span>Next &gt;</span>)}
      </div>
    </div>
  );
};

export default HomePageAdmin;
