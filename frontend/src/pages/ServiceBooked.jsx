import React, { useEffect, useState } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";
import { FaPlus, FaTrash } from "react-icons/fa";
import "../styles/ServiceBooked.css";
import { API_BASE } from "../api";

const ServiceBooked = () => {
  const [bookings, setBookings] = useState([]);
  const [loading, setLoading] = useState(true);
  const navigate = useNavigate();

  useEffect(() => {
    fetchBookings();
  }, []); // Fetch bookings on component mount

  const fetchBookings = async () => {
    try {
      const token = localStorage.getItem("token");
      const res = await axios.get(`${API_BASE}/provider-bookings`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      const bookingsData = res.data.bookings || [];
      setBookings(bookingsData);
    } catch (error) {
      console.error("Error fetching bookings:", error);
    } finally {
      setLoading(false);
    }
  };

  const handleAction = async (id, action) => {
    try {
      const token = localStorage.getItem("token");
      await axios.put(
        `${API_BASE}/bookings/${id}/${action}`,
        {},
        { headers: { Authorization: `Bearer ${token}` } }
      );

      // Refresh bookings to get updated database state
      fetchBookings();
    } catch (error) {
      console.error(`Error performing ${action}:`, error);
      alert(`Failed to ${action} booking.`);
    }
  };

  const handleMarkAllAvailable = async () => {
    try {
      const token = localStorage.getItem("token");
      
      // Use the backend endpoint to mark all services available
      await axios.put(
        `${API_BASE}/provider/services/mark-all-available`,
        {},
        { headers: { Authorization: `Bearer ${token}` } }
      );
      
      // Refresh bookings to get updated is_booked status
      fetchBookings();
      
      alert("All services marked as available!");
    } catch (error) {
      console.error("Error marking all services available:", error);
      alert("Failed to mark all services as available.");
    }
  };

  if (loading) {
    return <p className="text-center mt-4">Loading bookings...</p>;
  }

  // Calculate if all services are available based on database state
  // If any booking's service is still marked as booked (is_booked = 1), services are unavailable
  const allServicesAvailable = !bookings.some(booking => Number(booking.is_booked) === 1);

  return (
    <div className="service-page-container">
      <div className="service-booked-container">
        <div className="service-header">
          <div className="service-title-section">
            <h1>Service Management</h1>
            <p>Manage your services and bookings</p>
          </div>
          
          <div className="service-actions-section">
            <button 
              className="service-action-btn create-btn"
              onClick={() => navigate('/service-form')}
            >
              <FaPlus /> Create Service
            </button>
            <button 
              className="service-action-btn delete-btn"
              onClick={() => navigate('/delete-my-service')}
            >
              <FaTrash /> Manage Services
            </button>
            <button 
              className={`service-action-btn available-btn ${!allServicesAvailable ? 'inactive' : ''}`}
              onClick={handleMarkAllAvailable}
              disabled={allServicesAvailable}
            >
              {allServicesAvailable ? 'All Services Available' : 'Mark All Available'}
            </button>
          </div>
        </div>

        <div className="bookings-section">
          <div className="bookings-header">
            <h2>Service Bookings</h2>
            <div className={`service-status-indicator ${allServicesAvailable ? 'available' : 'unavailable'}`}>
              <span className="status-dot"></span>
              {allServicesAvailable ? 'All Services Available' : 'Services Unavailable'}
            </div>
          </div>

        {bookings.length === 0 ? (
          <p className="no-bookings">No bookings found for your services.</p>
        ) : (
          <div className="table-wrapper">
            <table className="service-table">
              <thead>
                <tr>
                  <th>Booking ID</th>
                  <th>Service Name</th>
                  <th>Booked By</th>
                  <th>Booking Time</th>
                  <th>Status</th>
                  <th>Payment</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                {bookings.map((booking) => {
                  const isConfirmed = Number(booking.status) === 1;
                  const isServiceAvailable = Number(booking.is_booked) === 0;

                  return (
                    <tr key={booking.booking_id}>
                      <td>{booking.booking_id}</td>
                      <td>{booking.service_name}</td>
                      <td>{booking.booked_by}</td>
                      <td>{booking.booking_time}</td>
                      <td>
                        {isConfirmed ? (
                          <span className="status-confirmed">Confirmed</span>
                        ) : (
                          <span className="status-pending">Pending</span>
                        )}
                      </td>
                      <td>
                        {booking.payment_status ? (
                          <span className="payment-paid">Paid</span>
                        ) : (
                          <span className="payment-unpaid">Unpaid</span>
                        )}
                      </td>
                      <td>
                        {!isConfirmed && (
                          <button
                            onClick={() => handleAction(booking.booking_id, "confirm")}
                            className="action-button button-confirm"
                          >
                            Confirm
                          </button>
                        )}

                        {!isConfirmed && (
                          <button
                            onClick={() => handleAction(booking.booking_id, "cancel")}
                            className="action-button button-cancel"
                          >
                            Cancel
                          </button>
                        )}
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        )}
        </div>
      </div>

      <footer className="footer">
        &copy; 2025 My Service Platform. All rights reserved.
      </footer>
    </div>
  );
};

export default ServiceBooked;
