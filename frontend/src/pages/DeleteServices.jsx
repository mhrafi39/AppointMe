import React, { useEffect, useState } from "react";
import axios from "axios";
import "../styles/DeleteServices.css";
import { API_BASE } from "../api";

const DeleteServices = () => {
  const [services, setServices] = useState([]);

  // 🔑 Determine auth headers for user/admin
  const getAuthHeaders = () => {
    const adminToken = localStorage.getItem("admin_token");
    const userToken = localStorage.getItem("token");
    const token = adminToken || userToken;
    return {
      Authorization: token ? `Bearer ${token}` : "",
    };
  };

  // 🔹 Determine delete URL based on token type
  const getDeleteUrl = (id) => {
    const adminToken = localStorage.getItem("admin_token");
    return adminToken
      ? `${API_BASE}/admin/services/${id}` // Admin route
      : `${API_BASE}/services/${id}`;      // User route
  };

  // Fetch services
  const fetchServices = async () => {
    try {
      const headers = { headers: getAuthHeaders() };
      const adminToken = localStorage.getItem("admin_token");

      // Correct URLs: admin vs user
      const url = adminToken
        ? `${API_BASE}/admin-services` // Admin sees all services
        : `${API_BASE}/my-services`;  // User sees only their own

      console.log("Fetching services from:", url);
      const res = await axios.get(url, headers);
      setServices(res.data.data || []);
    } catch (err) {
      console.error("Fetch services failed:", err.response || err);
    }
  };

  // Delete service
  const deleteService = async (id) => {
    if (!window.confirm("Are you sure you want to delete this service?")) return;

    try {
      await axios.delete(getDeleteUrl(id), { headers: getAuthHeaders() });
      setServices(services.filter((service) => service.services_id !== id));
    } catch (err) {
      console.error("Delete service failed:", err.response || err);
      alert("Failed to delete service");
    }
  };

  useEffect(() => {
    fetchServices();
  }, []);

  return (
    <div className="delete-services-container">
      <h1>Services</h1>
      {services.length === 0 ? (
        <p>No services found.</p>
      ) : (
        <ul>
          {services.map((service) => (
            <li key={service.services_id}>
              <strong>{service.name}</strong> - {service.location}
              <button
                className="delete-btn"
                onClick={() => deleteService(service.services_id)}
              >
                Delete
              </button>
            </li>
          ))}
        </ul>
      )}
    </div>
  );
};

export default DeleteServices;
