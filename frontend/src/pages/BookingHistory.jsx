

import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import { FaStar, FaEdit } from "react-icons/fa";
import "../styles/BookingHistory.css"; // Import the CSS file
import { API_BASE, STORAGE_BASE } from "../api";


const BookingHistory = () => {
  const navigate = useNavigate();
  const [bookings, setBookings] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [showReviewModal, setShowReviewModal] = useState(false);
  const [selectedBooking, setSelectedBooking] = useState(null);
  const [review, setReview] = useState({
    rating: "",
    comment: ""
  });

  useEffect(() => {
    const fetchBookings = async () => {
      try {
        const token = localStorage.getItem("token");
        console.log("Token exists:", !!token); // Debug log
        
        if (!token) {
          setError("You must be logged in to view bookings.");
          setLoading(false);
          return;
        }

        const res = await axios.get(`${API_BASE}/my-bookings`, {
          headers: { Authorization: `Bearer ${token}` },
        });

        console.log("API Response:", res.data); // Debug log
        console.log("API Response Success:", res.data.success); // Debug log
        console.log("API Response Bookings:", res.data.bookings); // Debug log
        
        if (res.data.success) {
          setBookings(res.data.bookings || []);
          console.log("Bookings set successfully:", res.data.bookings?.length || 0, "items"); // Debug log
        } else {
          console.log("API returned success=false:", res.data.message); // Debug log
          setError(res.data.message || "Failed to fetch bookings.");
        }
      } catch (err) {
        console.error("Error fetching bookings:", err);
        console.error("Error response:", err.response?.data);
        
        if (err.response && err.response.status === 401) {
          localStorage.removeItem("token");
          setError("Your session has expired. Please log in again.");
          setTimeout(() => {
            navigate("/login");
          }, 2000);
        } else {
          const errorMessage = err.response?.data?.message || "Failed to fetch bookings.";
          setError(errorMessage);
        }
      } finally {
        setLoading(false);
      }
    };

    fetchBookings();
  }, [navigate]);

  const openReviewModal = (booking) => {
    // Check if user has already reviewed this service
    if (booking.has_reviewed) {
      setError("You have already reviewed this service.");
      return;
    }
    
    // Check if payment is completed
    if (booking.payment_status.toLowerCase() !== 'paid') {
      setError("You can only review services after payment is completed.");
      return;
    }
    
    setSelectedBooking(booking);
    setShowReviewModal(true);
    setError(""); // Clear any previous errors
  };

  const closeReviewModal = () => {
    setShowReviewModal(false);
    setSelectedBooking(null);
    setReview({ rating: "", comment: "" });
  };

  const handleReviewChange = (e) => {
    setReview({ ...review, [e.target.name]: e.target.value });
  };

  const handleReviewSubmit = async (e) => {
    e.preventDefault();
    
    const token = localStorage.getItem("token");
    if (!token) {
      setError("You must be logged in to submit a review.");
      return;
    }

    try {
      const reviewData = {
        services_id: selectedBooking.services_id,
        rating: parseInt(review.rating),
        comment: review.comment
      };

      console.log("Submitting review:", reviewData); // Debug log

      const res = await axios.post(`${API_BASE}/addReview`, reviewData, {
        headers: { Authorization: `Bearer ${token}` },
      });

      console.log("Review response:", res.data); // Debug log

      if (res.data.success) {
        alert("Review submitted successfully!");
        
        // Update the bookings state to reflect the review submission
        setBookings(prevBookings => 
          prevBookings.map(booking => 
            booking.booking_id === selectedBooking.booking_id 
              ? { ...booking, has_reviewed: 1 }
              : booking
          )
        );
        
        closeReviewModal();
      } else {
        setError(res.data.message || "Failed to submit review.");
      }
    } catch (err) {
      console.error("Review submission error:", err);
      console.error("Error response:", err.response?.data);
      
      if (err.response && err.response.status === 401) {
        localStorage.removeItem("token");
        setError("Your session has expired. Please log in again.");
        setTimeout(() => {
          navigate("/login");
        }, 2000);
      } else if (err.response && err.response.status === 409) {
        // Handle duplicate review error
        setError("You have already reviewed this service.");
        closeReviewModal();
        
        // Update the booking state to show it's already reviewed
        setBookings(prevBookings => 
          prevBookings.map(booking => 
            booking.booking_id === selectedBooking.booking_id 
              ? { ...booking, has_reviewed: 1 }
              : booking
          )
        );
      } else {
        const errorMessage = err.response?.data?.message || "Failed to submit review.";
        setError(errorMessage);
      }
    }
  };

  if (loading) return <p className="info">Loading your bookings...</p>;
  if (error) return <p className="error">{error}</p>;
  
  console.log("Bookings data:", bookings); // Debug log
  console.log("Bookings length:", bookings?.length); // Debug log
  
  if (!bookings || bookings.length === 0) {
    return (
      <div className="booking-history-container">
        <div className="page-header">
          <h1 className="page-title">My Bookings</h1>
          <p className="page-subtitle">Track and manage all your service bookings</p>
        </div>
        <div className="empty-state">
          <p className="info">No bookings found. Book a service to see your booking history!</p>
        </div>
      </div>
    );
  }

  return (
    <div className="booking-history-container">
      <div className="page-header">
        <h1 className="page-title">My Bookings</h1>
        <p className="page-subtitle">Track and manage all your service bookings</p>
      </div>
      
      <div className="booking-grid">
        {bookings.map((b) => (
          <div className="booking-card modern" key={b.booking_id}>
            {/* Card Header */}
            <div className="card-header">
              <div className="provider-info">
                <img
                  src={
                    b.profile_picture && b.profile_picture !== 'default.jpeg'
                      ? (b.profile_picture.startsWith('http') ? b.profile_picture : `${STORAGE_BASE}/${b.profile_picture}`)
                      : "/default.jpeg"
                  }
                  alt="Provider"
                  className="provider-avatar"
                  onError={(e) => {
                    e.target.src = "/default.jpeg";
                  }}
                />
                <div className="provider-details">
                  <h3 className="service-title">{b.service_name || `Service #${b.services_id}`}</h3>
                  <p className="provider-name">by {b.provider_name}</p>
                </div>
              </div>
              <div className="booking-id">#{b.booking_id}</div>
            </div>

            {/* Card Body */}
            <div className="card-body">
              <div className="info-grid">
                <div className="info-item">
                  <span className="info-label">Price</span>
                  <span className="info-value price">৳{b.price}</span>
                </div>
                <div className="info-item">
                  <span className="info-label">Booking Time</span>
                  <span className="info-value">{new Date(b.booking_time).toLocaleString()}</span>
                </div>
                <div className="info-item">
                  <span className="info-label">Booked On</span>
                  <span className="info-value">{new Date(b.created_at).toLocaleDateString()}</span>
                </div>
              </div>

              {/* Status Badges */}
              <div className="status-row">
                <div className="status-badges">
                  <span className={`status-badge ${b.status.toLowerCase()}`}>
                    {b.status.charAt(0).toUpperCase() + b.status.slice(1)}
                  </span>
                  <span className={`payment-badge ${b.payment_status.toLowerCase()}`}>
                    {b.payment_status.charAt(0).toUpperCase() + b.payment_status.slice(1)}
                  </span>
                </div>
              </div>
            </div>

            {/* Card Footer */}
            <div className="card-footer">
              {/* Payment Button for unpaid bookings */}
              {b.payment_status.toLowerCase() === 'unpaid' && (
                <div className="footer-actions">
                  <button 
                    className="action-btn payment-btn"
                    onClick={() => navigate(`/payment/${b.booking_id}`)}
                  >
                    💳 Pay Now
                  </button>
                  <div className="payment-reminder">
                    Complete payment to leave a review
                  </div>
                </div>
              )}

              {/* Review Button - Only show for paid bookings that haven't been reviewed */}
              {b.payment_status.toLowerCase() === 'paid' && !b.has_reviewed && (
                <button 
                  className="action-btn review-btn"
                  onClick={() => openReviewModal(b)}
                  title="Leave a review for this service"
                >
                  <FaStar /> Leave Review
                </button>
              )}
              
              {/* Already Reviewed Status */}
              {b.payment_status.toLowerCase() === 'paid' && b.has_reviewed && (
                <div className="review-status completed">
                  <FaStar /> Review Submitted
                </div>
              )}
              
              {/* Payment Required Message */}
              {b.payment_status.toLowerCase() === 'unpaid' && (
                <div className="review-status disabled">
                  <span>Complete payment to review</span>
                </div>
              )}
            </div>
          </div>
        ))}
      </div>

      {/* Review Modal */}
      {showReviewModal && selectedBooking && (
        <div className="modal-overlay" onClick={closeReviewModal}>
          <div className="modal-content" onClick={(e) => e.stopPropagation()}>
            <div className="modal-header">
              <div className="modal-title-section">
                <h3>Leave a Review</h3>
                <p>Share your experience with this service</p>
              </div>
              <button className="modal-close" onClick={closeReviewModal}>&times;</button>
            </div>
            
            <div className="modal-body">
              <div className="service-preview">
                <div className="service-info">
                  <strong>{selectedBooking.service_name}</strong>
                  <span>by {selectedBooking.provider_name}</span>
                </div>
                <div className="service-price">৳{selectedBooking.price}</div>
              </div>
              
              <form onSubmit={handleReviewSubmit}>
                <div className="form-group">
                  <label>Rating</label>
                  <div className="rating-input">
                    <select
                      name="rating"
                      value={review.rating}
                      onChange={handleReviewChange}
                      required
                    >
                      <option value="">Select Rating</option>
                      <option value="1">⭐ 1 Star - Poor</option>
                      <option value="2">⭐⭐ 2 Stars - Fair</option>
                      <option value="3">⭐⭐⭐ 3 Stars - Good</option>
                      <option value="4">⭐⭐⭐⭐ 4 Stars - Very Good</option>
                      <option value="5">⭐⭐⭐⭐⭐ 5 Stars - Excellent</option>
                    </select>
                  </div>
                </div>
                
                <div className="form-group">
                  <label>Your Review</label>
                  <textarea
                    name="comment"
                    value={review.comment}
                    onChange={handleReviewChange}
                    placeholder="Describe your experience with this service..."
                    rows="4"
                    required
                  ></textarea>
                  <div className="char-count">{review.comment.length}/500 characters</div>
                </div>
                
                <div className="modal-actions">
                  <button type="button" className="btn-cancel" onClick={closeReviewModal}>
                    Cancel
                  </button>
                  <button type="submit" className="btn-submit">
                    Submit Review
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default BookingHistory;