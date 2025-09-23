import React, { useState, useEffect } from "react";
import axios from "axios";
import { ThreeDot } from "react-loading-indicators";
import "../styles/SettingsPage.css";
import logoImage from "../assets/servora-logo.jpg"; // same logo as login
import { API_BASE } from "../api";

const SettingsPage = () => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [currentPassword, setCurrentPassword] = useState("");
  const [newPassword, setNewPassword] = useState("");
  const [confirmNewPassword, setConfirmNewPassword] = useState("");

  const [savingName, setSavingName] = useState(false);
  const [savingEmail, setSavingEmail] = useState(false);
  const [savingPassword, setSavingPassword] = useState(false);

  const [message, setMessage] = useState(null);
  const token = localStorage.getItem("token");

  useEffect(() => {
    const fetchProfile = async () => {
      if (!token) {
        setLoading(false);
        return;
      }
      try {
        const res = await axios.get(`${API_BASE}/profile`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        if (res.data.success) {
          setUser(res.data.user);
          setName(res.data.user.name || "");
          setEmail(res.data.user.email || "");
        } else {
          setUser(null);
        }
      } catch (err) {
        console.error(err);
        setMessage({ type: "error", text: "Failed to load profile." });
      } finally {
        setLoading(false);
      }
    };
    fetchProfile();
  }, [token]);

  const notify = (type, text) => setMessage(type ? { type, text } : null);

  const handleUpdateName = async () => {
    notify(null, null);
    if (!name.trim()) return notify("error", "Name cannot be empty.");
    setSavingName(true);
    try {
      await axios.put(
        `${API_BASE}/settings/update-name`,
        { name },
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setUser((p) => ({ ...p, name }));
      notify("success", "Name updated successfully.");
    } catch (err) {
      console.error(err);
      notify("error", err.response?.data?.msg || "Failed to update name.");
    } finally {
      setSavingName(false);
    }
  };

  const handleUpdateEmail = async () => {
    notify(null, null);
    if (!email.trim()) return notify("error", "Email cannot be empty.");
    setSavingEmail(true);
    try {
      await axios.put(
        `${API_BASE}/settings/update-email`,
        { email },
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setUser((p) => ({ ...p, email }));
      notify("success", "Email updated successfully.");
    } catch (err) {
      console.error(err);
      notify("error", err.response?.data?.msg || "Failed to update email.");
    } finally {
      setSavingEmail(false);
    }
  };

  const handleChangePassword = async () => {
    notify(null, null);
    if (!currentPassword || !newPassword || !confirmNewPassword) {
      return notify("error", "All password fields are required.");
    }
    if (newPassword !== confirmNewPassword) {
      return notify("error", "New password confirmation does not match.");
    }
    setSavingPassword(true);
    try {
      await axios.put(
        `${API_BASE}/settings/update-password`,
        {
          current_password: currentPassword,
          new_password: newPassword,
          new_password_confirmation: confirmNewPassword,
        },
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setCurrentPassword("");
      setNewPassword("");
      setConfirmNewPassword("");
      notify("success", "Password changed successfully.");
    } catch (err) {
      console.error(err);
      notify("error", err.response?.data?.message || "Failed to change password.");
    } finally {
      setSavingPassword(false);
    }
  };

  const handleDeleteProfile = async () => {
    notify(null, null);
    if (!window.confirm("This action is permanent. Delete your account?")) return;
    try {
      await axios.delete(`${API_BASE}/settings/delete-account`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      localStorage.removeItem("token");
      alert("Your account has been deleted.");
      window.location.href = "/";
    } catch (err) {
      console.error(err);
      notify("error", err.response?.data?.msg || "Failed to delete account.");
    }
  };

  if (loading) return (
    <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '50vh', flexDirection: 'column' }}>
      <ThreeDot color={["#1d547a", "#2771a3", "#318dcc", "#59a4d7"]} />
      <p style={{ marginTop: '20px', color: '#6c757d' }}>Loading...</p>
    </div>
  );
  if (!user) return (
    <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '50vh', flexDirection: 'column' }}>
      <p style={{ color: '#6c757d' }}>Please login.</p>
    </div>
  );

  return (
    <section className="settings-section">
      <div className="settings-card">
        <img src={logoImage} alt="AppointMe Logo" className="settings-logo" />
        <h2 className="settings-title">Account Settings</h2>

        {message && (
          <div
            className={
              message.type === "success" ? "alert-success" : "alert-error"
            }
          >
            {message.text}
          </div>
        )}

        {/* Update Name */}
        <form
          className="settings-form"
          onSubmit={(e) => {
            e.preventDefault();
            handleUpdateName();
          }}
        >
          <label>Name</label>
          <input
            value={name}
            onChange={(e) => setName(e.target.value)}
            required
            className="input"
          />
          <button className="button" disabled={savingName}>
            {savingName ? "Saving..." : "Update Name"}
          </button>
        </form>

        {/* Update Email */}
        <form
          className="settings-form"
          onSubmit={(e) => {
            e.preventDefault();
            handleUpdateEmail();
          }}
        >
          <label>Email</label>
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
            className="input"
          />
          <button className="button" disabled={savingEmail}>
            {savingEmail ? "Saving..." : "Update Email"}
          </button>
        </form>

        {/* Change Password */}
        <form
          className="settings-form"
          onSubmit={(e) => {
            e.preventDefault();
            handleChangePassword();
          }}
        >
          <label>Current Password</label>
          <input
            type="password"
            value={currentPassword}
            onChange={(e) => setCurrentPassword(e.target.value)}
            required
            className="input"
          />
          <label>New Password</label>
          <input
            type="password"
            value={newPassword}
            onChange={(e) => setNewPassword(e.target.value)}
            required
            className="input"
          />
          <label>Confirm New Password</label>
          <input
            type="password"
            value={confirmNewPassword}
            onChange={(e) => setConfirmNewPassword(e.target.value)}
            required
            className="input"
          />
          <button className="button" disabled={savingPassword}>
            {savingPassword ? "Saving..." : "Change Password"}
          </button>
        </form>

        <button className="delete-btn" onClick={handleDeleteProfile}>
          Delete Account
        </button>
      </div>
    </section>
  );
};

export default SettingsPage;
