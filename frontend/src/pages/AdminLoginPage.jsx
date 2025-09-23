/*
Admin login credentials:
email: mahdikhan.chowdhury@gmail.com
pass: admin123
*/

import React, { useState } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";
import { ThreeDot } from "react-loading-indicators";
import "../styles/LoginPage.css"; // reuse user login styles
import { API_BASE } from "../api";

const AdminLoginPage = () => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [message, setMessage] = useState("");
  const [loading, setLoading] = useState(false);
  const navigate = useNavigate();

  const handleLogin = async (e) => {
    e.preventDefault();
    setMessage("");
    setLoading(true);

    try {
      const res = await axios.post(`${API_BASE}/admin/login`, {
        email,
        password,
      });

      if (res.data.token) {
        // Store admin token
        localStorage.setItem("admin_token", res.data.token);
        setMessage("Login successful!");

        // Redirect to unified homepage
        navigate("/homepage");
      } else {
        setMessage(res.data.msg || "Login failed");
      }
    } catch (err) {
      setMessage(err.response?.data?.msg || "Error logging in");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="login-container">
      <div className="login-card">
        <h2>Admin Login</h2>
        <form onSubmit={handleLogin} className="login-form">
          <input
            type="email"
            placeholder="Email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
          />
          <input
            type="password"
            placeholder="Password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
          />
          <button type="submit" disabled={loading}>
            {loading ? (
              <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '10px' }}>
                <ThreeDot color={["#ffffff", "#ffffff", "#ffffff", "#ffffff"]} size="small" />
                Logging in...
              </div>
            ) : "Login"}
          </button>
        </form>
        {message && <p className="login-message">{message}</p>}
      </div>
    </div>
  );
};

export default AdminLoginPage;
