import React, { useState, useEffect } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";
import { ThreeDot } from "react-loading-indicators";
import "../styles/ServiceForm.css";
import { API_BASE } from "../api";

export default function AddService() {
  const [formData, setFormData] = useState({
    name: "",
    description: "",
    category: "",
    location: "",
    price: "",
    available_time: "",
  });

  const [message, setMessage] = useState("");
  const [loading, setLoading] = useState(false);
  const [user, setUser] = useState(null);
  const navigate = useNavigate();

  // Redirect to login if not logged in and fetch user data
  useEffect(() => {
    const token = localStorage.getItem("token");
    if (!token) {
      navigate("/login");
      return;
    }

    // Fetch user profile to get user ID
    const fetchUser = async () => {
      try {
        const response = await axios.get(`${API_BASE}/profile`, {
          headers: { Authorization: `Bearer ${token}` }
        });
        if (response.data.success) {
          setUser(response.data.user);
        }
      } catch (error) {
        console.error("Failed to fetch user:", error);
        if (error.response?.status === 401) {
          localStorage.removeItem("token");
          navigate("/login");
        }
      }
    };

    fetchUser();
  }, [navigate]);

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setMessage("");
    setLoading(true);

    const token = localStorage.getItem("token");
    if (!token) {
      setMessage("❌ You must be logged in to create a service.");
      navigate("/login");
      return;
    }

    if (!user?.id) {
      setMessage("❌ User information not loaded. Please refresh the page.");
      setLoading(false);
      return;
    }

    try {
      const response = await axios.post(
        `${API_BASE}/services`,
        {
          ...formData,
          user_id: user.id // Automatically use logged-in user's ID
        },
        {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        }
      );

      if (response.data.success) {
        setMessage("✅ Service created successfully!");
        setFormData({
          name: "",
          description: "",
          category: "",
          location: "",
          price: "",
          available_time: "",
        });
        setTimeout(() => {
          navigate("/homepage");
        }, 2000);
      }
    } catch (error) {
      if (error.response && error.response.data.errors) {
        const errors = Object.values(error.response.data.errors).flat();
        setMessage("❌ " + errors.join(", "));
      } else if (error.response && error.response.status === 401) {
        setMessage("❌ Unauthorized. Please login again.");
        localStorage.removeItem("token");
        navigate("/login");
      } else {
        setMessage("❌ Failed to create service. Please try again.");
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="service-form-container">
      <div className="service-form-card">
        <div className="form-header">
          <h2>Create New Service</h2>
          <p>Fill out the details below to list your service</p>
          {user && (
            <div className="user-info">
              <span>Creating as: <strong>{user.name}</strong></span>
            </div>
          )}
        </div>

        {message && (
          <div className={`message ${message.includes('✅') ? 'success' : 'error'}`}>
            {message}
          </div>
        )}

        <form onSubmit={handleSubmit} className="service-form">
          <div className="form-row">
            <div className="input-group">
              <label htmlFor="name">Service Name *</label>
              <input
                type="text"
                id="name"
                name="name"
                value={formData.name}
                onChange={handleChange}
                placeholder="e.g., Professional House Cleaning"
                required
              />
            </div>

            <div className="input-group">
              <label htmlFor="category">Category *</label>
              <select
                id="category"
                name="category"
                value={formData.category}
                onChange={handleChange}
                required
              >
                <option value="">Select a category</option>
                <option value="Cleaning">Cleaning</option>
                <option value="Plumbing">Plumbing</option>
                <option value="Electrical">Electrical</option>
                <option value="Gardening">Gardening</option>
                <option value="Painting">Painting</option>
                <option value="Carpentry">Carpentry</option>
                <option value="Tutoring">Tutoring</option>
                <option value="Catering">Catering</option>
                <option value="Photography">Photography</option>
                <option value="Other">Other</option>
              </select>
            </div>
          </div>

          <div className="input-group">
            <label htmlFor="description">Description *</label>
            <textarea
              id="description"
              name="description"
              value={formData.description}
              onChange={handleChange}
              placeholder="Describe your service in detail..."
              rows="4"
              required
            />
          </div>

          <div className="form-row">
            <div className="input-group">
              <label htmlFor="location">Location *</label>
              <input
                type="text"
                id="location"
                name="location"
                value={formData.location}
                onChange={handleChange}
                placeholder="e.g., Dhaka, Bangladesh"
                required
              />
            </div>

            <div className="input-group">
              <label htmlFor="price">Price (BDT) *</label>
              <input
                type="number"
                id="price"
                name="price"
                value={formData.price}
                onChange={handleChange}
                placeholder="0"
                min="0"
                required
              />
            </div>
          </div>

          <div className="input-group">
            <label htmlFor="available_time">Available Time *</label>
            <input
              type="text"
              id="available_time"
              name="available_time"
              value={formData.available_time}
              onChange={handleChange}
              placeholder="e.g., 9:00 AM - 6:00 PM, Monday to Friday"
              required
            />
          </div>

          <div className="form-actions">
            <button
              type="button"
              className="btn-secondary"
              onClick={() => navigate("/homepage")}
              disabled={loading}
            >
              Cancel
            </button>
            <button
              type="submit"
              className="btn-primary"
              disabled={loading}
            >
              {loading ? (
                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '10px' }}>
                  <ThreeDot color={["#ffffff", "#ffffff", "#ffffff", "#ffffff"]} size="small" />
                  Creating...
                </div>
              ) : "Create Service"}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
