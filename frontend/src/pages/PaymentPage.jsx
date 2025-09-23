import React, { useState, useEffect } from "react";
import axios from "axios";
import { useNavigate, useParams, useLocation } from "react-router-dom";
import { FaWallet, FaMoneyBillWave, FaCheckCircle, FaArrowLeft } from "react-icons/fa";
import { API_BASE } from "../api";
import "../styles/PaymentPage.css";

function PaymentPage() {
  const { bookingId } = useParams();
  const navigate = useNavigate();
  const location = useLocation();
  
  const [serviceDetails, setServiceDetails] = useState(null);
  const [formData, setFormData] = useState({
    booking_id: bookingId || "",
    payment_method: "cash",
    amount_paid: "",
  });

  const [message, setMessage] = useState(null);
  const [error, setError] = useState(null);

  // Get service details from location state or fetch from API
  useEffect(() => {
    if (location.state) {
      setServiceDetails(location.state);
      setFormData(prev => ({
        ...prev,
        amount_paid: location.state.servicePrice || ""
      }));
    } else if (bookingId) {
      // Fetch booking details if no state passed
      fetchBookingDetails();
    }
  }, [location.state, bookingId]);

  const fetchBookingDetails = async () => {
    const token = localStorage.getItem("token");
    if (!token) {
      navigate("/login");
      return;
    }

    try {
      const res = await axios.get(`${API_BASE}/my-bookings`, {
        headers: { Authorization: `Bearer ${token}` }
      });
      
      if (res.data.success) {
        const booking = res.data.bookings.find(b => b.booking_id === parseInt(bookingId));
        if (booking) {
          setServiceDetails({
            serviceName: booking.service_name,
            servicePrice: booking.price || 0,
            bookingId: booking.booking_id
          });
          setFormData(prev => ({
            ...prev,
            amount_paid: booking.price || ""
          }));
        }
      }
    } catch (err) {
      setError("Failed to fetch booking details");
    }
  };

  // Redirect if no token
  useEffect(() => {
    const token = localStorage.getItem("token");
    if (!token) navigate("/login");
  }, [navigate]);

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setMessage(null);
    setError(null);

    const token = localStorage.getItem("token");
    if (!token) {
      setError("No token found. Please login first.");
      navigate("/login");
      return;
    }

    try {
      const res = await axios.post(
        `${API_BASE}/addPayment`,
        formData,
        {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        }
      );

      if (res.data.success) {
        setMessage(res.data.message || "Payment successful!");
        setTimeout(() => navigate("/booking-history"), 2000);
      } else {
        setError(res.data.message || "Payment failed.");
      }
    } catch (err) {
      console.log(err.response?.data);
      setError(
        err.response?.data?.message ||
          "Payment failed. Please try again."
      );
    }
  };

  return (
    <div className="payment-container">
      <div className="payment-wrapper">
        {/* Header */}
        <div className="payment-header">
          <button 
            className="back-btn"
            onClick={() => navigate(-1)}
            type="button"
          >
            <FaArrowLeft />
          </button>
          <div className="header-content">
            <FaWallet className="header-icon" />
            <h1>Confirm Payment</h1>
            <p>Review your order and complete payment</p>
          </div>
        </div>

        {/* Main Content - Side by Side Layout */}
        <div className="payment-main">
          {/* Left Side - Payment Form */}
          <div className="payment-form">
            <h3>Payment Details</h3>
            
            {message && (
              <div className="alert success">
                <FaCheckCircle />
                {message}
              </div>
            )}
            
            {error && (
              <div className="alert error">
                {error}
              </div>
            )}

            <form onSubmit={handleSubmit}>
              <div className="input-group">
                <label>Booking ID</label>
                <input
                  type="number"
                  name="booking_id"
                  value={formData.booking_id}
                  onChange={handleChange}
                  placeholder="Enter booking ID"
                  required
                  readOnly
                />
              </div>

              <div className="input-group">
                <label>Payment Method</label>
                <div className="payment-method-container">
                  <div className="payment-method selected">
                    <FaMoneyBillWave className="method-icon" />
                    <div className="method-details">
                      <span className="method-name">Cash Payment</span>
                      <span className="method-desc">Pay directly to the service provider</span>
                    </div>
                    <input
                      type="radio"
                      name="payment_method"
                      value="cash"
                      checked={formData.payment_method === "cash"}
                      onChange={handleChange}
                    />
                  </div>
                </div>
              </div>

              <div className="input-group">
                <label>Amount to Pay</label>
                <div className="amount-input">
                  <span className="currency">৳</span>
                  <input
                    type="number"
                    name="amount_paid"
                    value={formData.amount_paid}
                    onChange={handleChange}
                    placeholder="0.00"
                    step="0.01"
                    min="0"
                    required
                  />
                </div>
              </div>

              <button 
                type="submit" 
                className="pay-btn"
              >
                <FaCheckCircle />
                Confirm Cash Payment
              </button>
            </form>
          </div>

          {/* Right Side - Summary and Instructions */}
          <div className="payment-sidebar">
            {/* Service Summary */}
            {serviceDetails && (
              <div className="service-summary">
                <h3>Order Summary</h3>
                <div className="summary-details">
                  {serviceDetails.serviceName && (
                    <div className="summary-item">
                      <span className="label">Service:</span>
                      <span className="value">{serviceDetails.serviceName}</span>
                    </div>
                  )}
                  <div className="summary-item">
                    <span className="label">Booking ID:</span>
                    <span className="value">#{bookingId}</span>
                  </div>
                  {serviceDetails.servicePrice && (
                    <div className="summary-item total">
                      <span className="label">Total Amount:</span>
                      <span className="value">৳{serviceDetails.servicePrice}</span>
                    </div>
                  )}
                </div>
              </div>
            )}

            {/* Payment Instructions */}
            <div className="payment-info">
              <h4>Payment Instructions</h4>
              <ul>
                <li>Payment will be made directly to the service provider</li>
                <li>Please have the exact amount ready</li>
                <li>Payment confirmation will be sent after service completion</li>
                <li>Keep your booking ID for reference</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default PaymentPage;
