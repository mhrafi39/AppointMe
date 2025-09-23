import React, { useEffect, useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import axios from "axios";
import { FaCalendarAlt } from "react-icons/fa";
import { API_BASE } from "../api";
import "../styles/ServicePage.css";

export default function ServicePage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [service, setService] = useState(null);
  const [message, setMessage] = useState("");
  const [userId, setUserId] = useState(null);

  const token = localStorage.getItem("token");

  useEffect(() => {
    if (!token) navigate("/login");
  }, [navigate, token]);

  const [booking, setBooking] = useState({
    services_id: id,
    user_id: "",
    booking_time: "",
    status: false,
    payment_status: false,
  });

  useEffect(() => {
    if (token) {
      axios
        .get(`${API_BASE}/profile`, { headers: { Authorization: `Bearer ${token}` } })
        .then((res) => {
          if (res.data.success) {
            setUserId(res.data.user.id);
            setBooking((prev) => ({ ...prev, user_id: res.data.user.id }));
          }
        })
        .catch(() => {
          setMessage("❌ Failed to fetch user info. Please login again.");
          localStorage.removeItem("token");
          navigate("/login");
        });
    }
  }, [token, navigate]);

  useEffect(() => {
    axios
      .get(`${API_BASE}/service/${id}`, { headers: token ? { Authorization: `Bearer ${token}` } : {} })
      .then((res) => {
        if (res.data.success) setService(res.data.service);
        else setMessage("❌ Failed to load service");
      })
      .catch(() => setMessage("❌ Failed to load service"));
  }, [id, token]);

  const handleBookingChange = (e) => setBooking({ ...booking, [e.target.name]: e.target.value });
  const authHeaders = () => ({ headers: { Authorization: token ? `Bearer ${token}` : "" } });

  const handleBookingSubmit = (e) => {
    e.preventDefault();
    if (!token) {
      setMessage("❌ Please login first.");
      navigate("/login");
      return;
    }
    axios
      .post(`${API_BASE}/addBookings`, booking, authHeaders())
      .then((res) => {
        if (res.data.success) {
          setMessage("✅ Booking completed successfully! Redirecting to payment...");
          // Redirect to payment page with booking ID and service details
          setTimeout(() => {
            navigate(`/payment/${res.data.booking.booking_id}`, {
              state: { 
                serviceId: id, 
                serviceName: service.name,
                servicePrice: service.price,
                bookingId: res.data.booking.booking_id
              }
            });
          }, 1500);
        }
      })
      .catch((err) => {
        if (err.response && err.response.status === 401) {
          setMessage("❌ Unauthorized. Please login again.");
          localStorage.removeItem("token");
          navigate("/login");
        } else setMessage("❌ Booking failed");
      });
  };

  if (!service) return <p>Loading service...</p>;

  return (
    <div className="container">
      {message && <p className="message">{message}</p>}

      {/* Left column */}
      <div className="left-column">
        <h2 className="section-title">Service Info</h2>
        <div className="info-card">
          <h3><strong>Name:</strong> {service.name}</h3>
          <h3><strong>Category:</strong> {service.category}</h3>
          <h3><strong>Location:</strong> {service.location}</h3>
          <h3><strong>Price:</strong> {service.price} Tk</h3>
          <h3><strong>Available Time:</strong> {service.available_time}</h3>
        </div>
      </div>

      {/* Right column */}
      <div className="right-column">
        <h2 className="section-title">Actions</h2>

        <div className="form-container modern">
          <div className="form-header">
            <FaCalendarAlt className="icon" />
            <h3>Booking</h3>
          </div>
          <form onSubmit={handleBookingSubmit}>
            <div className="input-wrapper">
              <label>Category</label>
              <input type="text" value={service.category} readOnly />
            </div>
            <div className="input-wrapper">
              <label>Booking time</label>
              <input
                type="time"
                name="booking_time"
                value={booking.booking_time}
                onChange={handleBookingChange}
                required
              />
            </div>
            <button type="submit">Book</button>
          </form>
        </div>
      </div>
    </div>
  );
}
