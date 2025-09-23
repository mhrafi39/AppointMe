import React, { useEffect, useState } from "react";
import axios from "axios";
import { FaBell, FaCheckCircle, FaTimesCircle } from "react-icons/fa";
import "../styles/Notification.css";
import { API_BASE } from "../api";

const NotificationsPage = () => {
  const [notifications, setNotifications] = useState([]);
  const [loading, setLoading] = useState(true);

  const token = localStorage.getItem("token") || localStorage.getItem("admin_token");

  useEffect(() => {
    if (!token) return;

    const fetchNotifications = async () => {
      try {
        const res = await axios.get(`${API_BASE}/notifications`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        setNotifications(res.data.notifications || []);
      } catch (err) {
        console.error("Failed to fetch notifications:", err);
      } finally {
        setLoading(false);
      }
    };

    fetchNotifications();
    const interval = setInterval(fetchNotifications, 5000); // poll every 5s
    return () => clearInterval(interval);
  }, [token]);

  const renderIcon = (type) => {
    switch (type) {
      case "booking": return <FaBell className="notif-icon booking" />;
      case "cancel": return <FaTimesCircle className="notif-icon cancel" />;
      case "approval": return <FaCheckCircle className="notif-icon approval" />;
      default: return <FaBell className="notif-icon default" />;
    }
  };

  return (
    <div className="notifications-page">
      <h2 className="notif-title">Notifications</h2>

      {loading ? (
        <p className="notif-loading">Loading notifications...</p>
      ) : notifications.length === 0 ? (
        <p className="notif-empty">No notifications yet.</p>
      ) : (
        <ul className="notif-list">
          {notifications.map((notif) => (
            <li key={notif.id} className="notif-item">
              {renderIcon(notif.type)}
              <div className="notif-content">
                <p className="notif-message">{notif.message}</p>
                <span className="notif-time">{new Date(notif.created_at).toLocaleString()}</span>
              </div>
            </li>
          ))}
        </ul>
      )}
    </div>
  );
};

export default NotificationsPage;
