import { useState } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";
import { ThreeDot } from "react-loading-indicators";
import ServoraLogo from "../assets/servora-logo.jpg";
import "../styles/SignupPage.css";
import { API_BASE } from "../api";

const SignupPage = () => {
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
  });

  const [errors, setErrors] = useState({});
  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState("");

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const validateForm = () => {
    const newErrors = {};
    if (!formData.name.trim()) newErrors.name = "Name is required";
    if (!formData.email.trim()) newErrors.email = "Email is required";
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email))
      newErrors.email = "Invalid email format";
    if (!formData.password) newErrors.password = "Password is required";
    if (formData.password !== formData.password_confirmation)
      newErrors.password_confirmation = "Passwords do not match";
    return newErrors;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    const newErrors = validateForm();

    if (Object.keys(newErrors).length === 0) {
      setLoading(true);
      setErrors({});
      setMessage("");

      try {
        const res = await axios.post(`${API_BASE}/signup`, {
          name: formData.name,
          email: formData.email,
          password: formData.password,
        });

        if (res.data.success) {
          localStorage.setItem("token", res.data.token);
          localStorage.setItem("user", JSON.stringify(res.data.user));

          setMessage("Registration successful! Redirecting...");
          setTimeout(() => navigate("/homepage"), 2000);
        } else {
          setMessage(res.data.msg || "Signup failed");
        }
      } catch (err) {
        setMessage(err.response?.data?.msg || "Error signing up");
      } finally {
        setLoading(false);
      }
    } else {
      setErrors(newErrors);
    }
  };

  return (
    <div className="register-page">
      <div className="register-card">
        <img src={ServoraLogo} alt="AppointMe" className="register-logo" />

        {message && <p className="signup-message">{message}</p>}
        {errors.apiError && <p className="error-message">{errors.apiError}</p>}

        <form onSubmit={handleSubmit} className="register-form">
          {["name", "email", "password", "password_confirmation"].map(
            (field, i) => {
              const placeholders = [
                "John Doe",
                "example@example.com",
                "••••••••",
                "••••••••",
              ];
              const labels = ["Name", "Email", "Password", "Confirm Password"];
              return (
                <div key={field} className="form-group">
                  <label htmlFor={field}>{labels[i]}</label>
                  <input
                    type={field.includes("password") ? "password" : "text"}
                    id={field}
                    name={field}
                    placeholder={placeholders[i]}
                    value={formData[field]}
                    onChange={handleChange}
                  />
                  {errors[field] && (
                    <p className="input-error">{errors[field]}</p>
                  )}
                </div>
              );
            }
          )}

          <button
            type="submit"
            disabled={loading}
            className={`submit-btn ${loading ? "loading" : ""}`}
          >
            {loading ? (
              <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '10px' }}>
                <ThreeDot color={["#ffffff", "#ffffff", "#ffffff", "#ffffff"]} size="small" />
                Registering...
              </div>
            ) : "Register"}
          </button>
        </form>

        <p className="login-text">
          Already have an account? <button className="login-link" onClick={() => navigate("/login")}>Login</button>
        </p>
      </div>
    </div>
  );
};

export default SignupPage;
