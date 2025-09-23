import { useState } from "react";
import axios from "axios";
import { useNavigate, Link } from "react-router-dom";
import { ThreeDot } from "react-loading-indicators";
import "../styles/LoginPage.css";
import logoImage from "../assets/servora-logo.jpg"; // Servora logo
import { API_BASE } from "../api";

const LoginPage = () => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [role, setRole] = useState("user"); // New role state
  const [message, setMessage] = useState("");
  const [loading, setLoading] = useState(false);
  const navigate = useNavigate();

  const handleLogin = async (e) => {
    e.preventDefault();
    setMessage(""); // reset message
    setLoading(true);

    try {
      // Determine the endpoint based on role
      const endpoint = role === "admin" 
        ? `${API_BASE}/admin/login`
        : `${API_BASE}/login`;

      const res = await axios.post(endpoint, {
        email,
        password,
      });

      if (res.data.token) {
        // Store token based on role
        if (role === "admin") {
          localStorage.setItem("admin_token", res.data.token);
        } else {
          localStorage.setItem("token", res.data.token);
          // Optional: store user info for regular users
          if (res.data.user) {
            localStorage.setItem("user", JSON.stringify(res.data.user));
          }
        }

        setMessage("Login successful!");
        navigate("/homepage"); // redirect after login
      } else {
        setMessage(res.data.msg || "Invalid credentials");
      }
    } catch (err) {
      console.error(err);
      setMessage(err.response?.data?.msg || "Error logging in");
    } finally {
      setLoading(false);
    }
  };

  return (
    <section className="login-section">
      <div className="login-card">
        <img src={logoImage} alt="AppointMe Logo" className="login-logo" />

        {message && <p className="error-text">{message}</p>}

        <form onSubmit={handleLogin} className="form">
          {/* Role Selector */}
          <div className="role-selector">
            <label className="label">Login as:</label>
            <div className="role-options">
              <label className="role-option">
                <input
                  type="radio"
                  name="role"
                  value="user"
                  checked={role === "user"}
                  onChange={(e) => setRole(e.target.value)}
                />
                <span className="role-text">User</span>
              </label>
              <label className="role-option">
                <input
                  type="radio"
                  name="role"
                  value="admin"
                  checked={role === "admin"}
                  onChange={(e) => setRole(e.target.value)}
                />
                <span className="role-text">Admin</span>
              </label>
            </div>
          </div>

          <div>
            <label htmlFor="email" className="label">
              Email:
            </label>
            <input
              type="email"
              id="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="example@example.com"
              className="input"
              required
            />
          </div>

          <div>
            <label htmlFor="password" className="label">
              Password:
            </label>
            <input
              type="password"
              id="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              placeholder="••••••••"
              className="input"
              required
            />
          </div>

          <button type="submit" className="button" disabled={loading}>
            {loading ? (
              <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '10px' }}>
                <ThreeDot color={["#ffffff", "#ffffff", "#ffffff", "#ffffff"]} size="small" />
                Logging in...
              </div>
            ) : `Login as ${role === "admin" ? "Admin" : "User"}`}
          </button>
        </form>

        <Link to="/signup" className="register-btn">
          Register Now
        </Link>
      </div>
    </section>
  );
};

export default LoginPage;
