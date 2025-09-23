import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { FaUser, FaEnvelope, FaShieldAlt } from "react-icons/fa";
import { Settings, ChevronDown, Trash2 } from "lucide-react";
import "../styles/AdminProfile.css";
import { API_BASE } from "../api";

const AdminProfile = () => {
  const [admin, setAdmin] = useState(null);
  const [showServiceDropdown, setShowServiceDropdown] = useState(false);
  const navigate = useNavigate();

  // Service management functions
  const handleDeleteAllServices = () => {
    setShowServiceDropdown(false);
    navigate("/delete-service");
  };

  useEffect(() => {
    const token = localStorage.getItem("admin_token");
    if (!token) {
      navigate("/admin-login");
      return;
    }

    fetch(`${API_BASE}/admin/profile`, {
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
    })
      .then(res => {
        if (res.status === 401) {
          throw new Error('Unauthorized');
        }
        return res.json();
      })
      .then(data => {
        if (data.success) {
          setAdmin(data.admin);
        } else {
          throw new Error(data.msg || 'Failed to fetch profile');
        }
      })
      .catch(err => {
        console.error("Failed to fetch admin profile:", err);
        localStorage.removeItem("admin_token");
        navigate("/admin-login");
      });

    // Close dropdown when clicking outside
    const handleClickOutside = (event) => {
      if (showServiceDropdown && !event.target.closest('.admin-service-dropdown-container')) {
        setShowServiceDropdown(false);
      }
    };
    
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, [navigate, showServiceDropdown]);

  if (!admin) {
    return (
      <div className="admin-profile-container">
        <div className="error-message">
          <p>Could not load admin profile.</p>
        </div>
      </div>
    );
  }

  return (
    <div className="admin-profile-container">
      <div className="admin-profile-header">
        <div className="header-content">
          <h1>Admin Profile</h1>
          <p>Manage your administrative account</p>
        </div>
        
        {/* Service Management Dropdown */}
        <div className="admin-service-dropdown-container">
          <button 
            className="admin-manage-btn" 
            onClick={() => setShowServiceDropdown(!showServiceDropdown)}
          >
            <Settings size={16} /> Manage Services <ChevronDown size={16} />
          </button>
          
          {showServiceDropdown && (
            <div className="admin-dropdown-menu">
              <button 
                className="admin-dropdown-item"
                onClick={handleDeleteAllServices}
              >
                <Trash2 size={16} color="#ef4444" /> 
                <span>Delete All Services</span>
              </button>
            </div>
          )}
        </div>
      </div>

      <div className="admin-profile-card">
        <div className="profile-avatar">
          <div className="avatar-placeholder">
            <FaUser />
          </div>
        </div>

        <div className="profile-info">
          <div className="info-item">
            <div className="info-icon">
              <FaUser />
            </div>
            <div className="info-content">
              <label>Full Name</label>
              <span>{admin.name}</span>
            </div>
          </div>

          <div className="info-item">
            <div className="info-icon">
              <FaEnvelope />
            </div>
            <div className="info-content">
              <label>Email Address</label>
              <span>{admin.email}</span>
            </div>
          </div>

          <div className="info-item">
            <div className="info-icon">
              <FaShieldAlt />
            </div>
            <div className="info-content">
              <label>Role</label>
              <span className="admin-badge">Administrator</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default AdminProfile;
