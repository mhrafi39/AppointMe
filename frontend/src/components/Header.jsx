import React, { useState, useEffect, useRef } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { FaUserCircle, FaBell, FaUser, FaHistory, FaCog, FaTachometerAlt, FaSignOutAlt } from 'react-icons/fa';
import defaultProfilePic from '../assets/defaultProfilePic.jpg';
import servoraLogo from '../assets/servora-logo.jpg'; // updated logo import
import { API_BASE } from '../api';

const Header = () => {
  const [isDropdownOpen, setIsDropdownOpen] = useState(false);
  const [isNotifOpen, setIsNotifOpen] = useState(false);
  const [notifications, setNotifications] = useState([]);
  const [currentUser, setCurrentUser] = useState(null);
  const navigate = useNavigate();
  const dropdownRef = useRef(null);
  const notifRef = useRef(null);

  const userToken = localStorage.getItem('token');
  const adminToken = localStorage.getItem('admin_token');
  const token = userToken || adminToken;
  const isLoggedInAsAdmin = !!adminToken;

  useEffect(() => {
    if (!token) return;

    const fetchUserData = async () => {
      const endpoint = isLoggedInAsAdmin ? '/admin/profile' : '/profile';
      try {
        const res = await axios.get(`${API_BASE}${endpoint}`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        const userData = isLoggedInAsAdmin ? res.data.admin : res.data.user;
        setCurrentUser(userData);
      } catch (error) {
        console.error('Failed to fetch user for header:', error);
        localStorage.removeItem(isLoggedInAsAdmin ? 'admin_token' : 'token');
      }
    };

    fetchUserData();
  }, [token, isLoggedInAsAdmin]);

  useEffect(() => {
    const handleClickOutside = (event) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target)) setIsDropdownOpen(false);
      if (notifRef.current && !notifRef.current.contains(event.target)) setIsNotifOpen(false);
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  useEffect(() => {
    if (!token) return;

    const fetchNotifications = async () => {
      try {
        // Only fetch notifications for regular users, not admins for now
        // since admin notifications endpoint doesn't exist
        if (isLoggedInAsAdmin) {
          setNotifications([]);
          return;
        }
        
        const res = await axios.get(`${API_BASE}/notifications`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        setNotifications(res.data.notifications || []);
      } catch (err) {
        console.error("Failed to fetch notifications:", err);
      }
    };

    fetchNotifications();
    const interval = setInterval(fetchNotifications, 5000);
    return () => clearInterval(interval);
  }, [token, isLoggedInAsAdmin]);

  useEffect(() => {
    if (isNotifOpen && !isLoggedInAsAdmin && notifications.some(n => n.is_read === 0)) {
      // Remove this automatic mark all as read behavior
      // We'll implement individual read marking instead
    }
  }, [isNotifOpen]);

  // Function to mark individual notification as read
  const markNotificationAsRead = async (notificationId) => {
    try {
      await axios.post(`${API_BASE}/notifications/${notificationId}/mark-read`, {}, {
        headers: { Authorization: `Bearer ${token}` },
      });
      
      // Update the local state to mark this notification as read
      setNotifications(prevNotifications => 
        prevNotifications.map(notif => 
          notif.id === notificationId 
            ? { ...notif, is_read: 1 }
            : notif
        )
      );
    } catch (err) {
      console.error('Failed to mark notification as read:', err);
    }
  };

  // Function to handle notification click
  const handleNotificationClick = (notificationId, isRead) => {
    if (isRead === 0) {
      markNotificationAsRead(notificationId);
    }
  };

  const handleLogout = () => {
    const logoutEndpoint = isLoggedInAsAdmin ? '/admin/logout' : '/logout';
    axios.post(`${API_BASE}${logoutEndpoint}`, {}, {
      headers: { Authorization: `Bearer ${token}` },
    }).finally(() => {
      localStorage.removeItem(isLoggedInAsAdmin ? 'admin_token' : 'token');
      navigate(isLoggedInAsAdmin ? '/admin-login' : '/login');
      window.location.reload();
    });
  };

  const profilePicture = currentUser?.profile_picture || defaultProfilePic;

  return (
    <header className="app-header">
      <div className="header-left">
        <Link to="/" className="logo">
          <img src={servoraLogo} alt="AppointMe Logo" className="logo-img" />
        </Link>
      </div>

      <div className="header-center">
        <nav className="main-nav">
          <Link to="/homepage" className="nav-link">Home</Link>
          <Link to="/about" className="nav-link">About Us</Link>
          <Link to="/contact" className="nav-link">Contact</Link>
        </nav>
      </div>

      <div className="header-right">
        {token && (
          <div className="notification-container" ref={notifRef}>
            <button className="notif-btn" onClick={() => setIsNotifOpen(!isNotifOpen)}>
              <FaBell size={22} color="#fff" />
              {notifications.filter(n => n.is_read === 0).length > 0 && (
                <span className="notif-badge">{notifications.filter(n => n.is_read === 0).length}</span>
              )}
            </button>

            {isNotifOpen && (
              <div className="notif-dropdown">
                <div className="notif-header">Notifications</div>
                <div className="notif-list">
                  {notifications.filter(n => n.is_read === 0).length > 0 ? (
                    notifications
                      .filter(n => n.is_read === 0)
                      .map((notif) => (
                        <div 
                          key={notif.id} 
                          className="notif-item"
                          onClick={() => handleNotificationClick(notif.id, notif.is_read)}
                          style={{ cursor: 'pointer' }}
                        >
                          <div className="notif-message">{notif.message}</div>
                          <div className="notif-time">{new Date(notif.created_at).toLocaleString()}</div>
                        </div>
                      ))
                  ) : (
                    <div className="notif-empty">No new notifications</div>
                  )}
                </div>
                <div className="notif-footer"><Link to="/notifications">View all</Link></div>
              </div>
            )}
          </div>
        )}

        <div className="profile-menu-container" ref={dropdownRef}>
          <button className={`profile-circle ${token ? 'logged-in' : ''}`} onClick={() => setIsDropdownOpen(!isDropdownOpen)}>
            {token ? <img src={profilePicture} alt="Profile" /> : <FaUserCircle size={30} color="#a0aec0" />}
          </button>

          {isDropdownOpen && (
            <div className="profile-dropdown">
              {!token ? (
                <>
                  <Link to="/login" className="dropdown-item">Login</Link>
                  <Link to="/signup" className="dropdown-item">Sign Up</Link>
                </>
              ) : isLoggedInAsAdmin ? (
                <>
                  <div className="dropdown-user-info"><strong>{currentUser?.name}</strong><span>Admin</span></div>
                  <div className="dropdown-separator"></div>
                  <Link to="/admin-dashboard" className="dropdown-item">
                    <FaTachometerAlt className="dropdown-icon" />
                    Dashboard
                  </Link>
                  <Link to="/admin-profile" className="dropdown-item">
                    <FaUser className="dropdown-icon" />
                    Profile
                  </Link>
                  <div className="dropdown-separator"></div>
                  <button onClick={handleLogout} className="dropdown-item dropdown-button">
                    <FaSignOutAlt className="dropdown-icon" />
                    Logout
                  </button>
                </>
              ) : (
                <>
                  <div className="dropdown-user-info"><strong>{currentUser?.name}</strong><span>{currentUser?.is_verified ? 'Provider' : 'User'}</span></div>
                  <div className="dropdown-separator"></div>
                  <Link to="/profile" className="dropdown-item">
                    <FaUser className="dropdown-icon" />
                    Profile
                  </Link>
                  {currentUser?.is_verified && (
                    <Link to="/services-booked" className="dropdown-item">
                      <FaTachometerAlt className="dropdown-icon" />
                      Service Management
                    </Link>
                  )}
                  <Link to="/booking-history" className="dropdown-item">
                    <FaHistory className="dropdown-icon" />
                    Booking History
                  </Link>
                  <Link to="/settings" className="dropdown-item">
                    <FaCog className="dropdown-icon" />
                    Settings
                  </Link>
                  <div className="dropdown-separator"></div>
                  <button onClick={handleLogout} className="dropdown-item dropdown-button">
                    <FaSignOutAlt className="dropdown-icon" />
                    Logout
                  </button>
                </>
              )}
            </div>
          )}
        </div>
      </div>
    </header>
  );
};

export default Header;
