import React, { useRef, useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import { ThreeDot } from "react-loading-indicators";
import "../styles/ServiceDropdown.css";
import { API_BASE } from "../api";
import {
  Star,
  ShieldCheck,
  MapPin,
  Phone,
  Mail,
  BadgeCheck,
  Clock,
  Camera,
  ChevronRight,
  CheckCircle,
  Pencil,
  X,
  Upload,
  Edit,
  Calendar,
  User as UserIcon,
  Plus,
  Trash2,
  Settings,
  ChevronDown
} from "lucide-react";
import "../styles/ProfilePage.css";
import defaultProfilePic from "../assets/defaultProfilePic.jpg";

// Provider Application Form Component
const ProviderForm = ({ onClose, onSubmit }) => {
  const [formData, setFormData] = useState({
    real_name: '',
    documents: null
  });

  return (
    <div className="servora-modal-overlay">
      <div className="servora-modal">
        <button onClick={onClose} className="servora-modal-close">
          <X size={20} />
        </button>
        
        <h3>Apply as Service Provider</h3>
        
        <form onSubmit={(e) => {
          e.preventDefault();
          onSubmit(formData);
        }}>
          <div className="servora-form-group">
            <label>Full Real Name</label>
            <input 
              type="text" 
              placeholder="Enter your full legal name"
              value={formData.real_name}
              onChange={(e) => setFormData({...formData, real_name: e.target.value})}
              required
            />
          </div>
          
          <div className="servora-form-group">
            <label>Upload Documents (ID Proof, Certificates)</label>
            <label className="servora-file-upload">
              <Upload size={18} />
              <span>{formData.documents ? formData.documents.name : 'Choose files'}</span>
              <input 
                type="file" 
                onChange={(e) => setFormData({...formData, documents: e.target.files[0]})}
                required
                hidden
              />
            </label>
          </div>
          
          <button type="submit" className="servora-btn servora-primary servora-wfull">
            Submit Application
          </button>
        </form>
      </div>
    </div>
  );
};

// Star Rating Component
const StarRow = ({ value = 0, size = 18 }) => {
  const full = Math.floor(value);
  const half = value - full >= 0.5;
  const stars = Array.from({ length: 5 });
  
  return (
    <div className="servora-star-row" title={`${value} out of 5`}>
      {stars.map((_, i) => {
        const fill = i < full ? "servora-full" : i === full && half ? "servora-half" : "servora-empty";
        return (
          <span key={i} className={`servora-star ${fill}`} style={{ width: size, height: size }}>
            <Star size={size} />
          </span>
        );
      })}
    </div>
  );
};

// Stat Card Component
const Stat = ({ icon: Icon, label, value }) => (
  <div className="servora-stat">
    <div className="servora-stat-icon">
      <Icon size={18} />
    </div>
    <div className="servora-stat-text">
      <div className="servora-stat-value">{value}</div>
      <div className="servora-stat-label">{label}</div>
    </div>
  </div>
);

// Pill Badge Component
const Pill = ({ children, icon: Icon }) => (
  <span className="servora-pill">
    {Icon && <Icon size={14} />}
    <span>{children}</span>
  </span>
);

// Review Item Component
const ReviewItem = ({ r }) => (
  <div className="servora-review-item">
    <div className="servora-review-top">
      <div className="servora-avatar">{r.name?.[0] || "U"}</div>
      <div>
        <div className="servora-review-name">{r.name}</div>
        <div className="servora-review-meta">
          <StarRow value={r.rating} size={14} />{" "}
          <span className="servora-dot">•</span> {r.date}
          {r.service_name && (
            <>
              <span className="servora-dot">•</span> 
              <span className="servora-service-name">{r.service_name}</span>
            </>
          )}
        </div>
      </div>
    </div>
    <p className="servora-review-text">{r.text}</p>
  </div>
);

// Tabs Component
const Tabs = ({ tabs, active, setActive }) => (
  <div className="servora-tabs">
    {tabs.map((t) => (
      <button
        key={t.key}
        className={`servora-tab ${active === t.key ? "servora-active" : ""}`}
        onClick={() => setActive(t.key)}
      >
        {t.icon && <t.icon size={16} />} {t.label}
      </button>
    ))}
  </div>
);

// Edit Profile Form Component
const EditProfileForm = ({ user, onClose, onSubmit }) => {
  const [formData, setFormData] = useState({
    name: user.name || '',
    phone: user.phone === 'Not specified' ? '' : (user.phone || ''),
    address: user.address === 'Not specified' ? '' : (user.address || ''),
    details: user.details === 'No information provided.' ? '' : (user.details || '')
  });

  return (
    <div className="servora-modal-overlay">
      <div className="servora-modal">
        <button onClick={onClose} className="servora-modal-close">
          <X size={20} />
        </button>
        
        <h3>Edit Profile</h3>
        
        <form onSubmit={(e) => {
          e.preventDefault();
          onSubmit(formData);
        }}>
          <div className="servora-form-group">
            <label>Full Name</label>
            <input 
              type="text" 
              placeholder="Enter your full name"
              value={formData.name}
              onChange={(e) => setFormData({...formData, name: e.target.value})}
              required
            />
          </div>
          
          <div className="servora-form-group">
            <label>Phone Number</label>
            <input 
              type="tel" 
              placeholder="Enter your phone number"
              value={formData.phone}
              onChange={(e) => setFormData({...formData, phone: e.target.value})}
            />
          </div>
          
          <div className="servora-form-group">
            <label>Location</label>
            <input 
              type="text" 
              placeholder="Enter your location"
              value={formData.address}
              onChange={(e) => setFormData({...formData, address: e.target.value})}
            />
          </div>
          
          <div className="servora-form-group">
            <label>About Me</label>
            <textarea 
              rows="4"
              placeholder="Tell us about yourself..."
              value={formData.details}
              onChange={(e) => setFormData({...formData, details: e.target.value})}
            />
          </div>
          
          <button type="submit" className="servora-btn servora-primary servora-wfull">
            Save Changes
          </button>
        </form>
      </div>
    </div>
  );
};

const ProfilePage = () => {
  const [user, setUser] = useState(null);
  const [bookingHistory, setBookingHistory] = useState([]);
  const [loading, setLoading] = useState(true);
  const [loadingBookings, setLoadingBookings] = useState(false);
  const [error, setError] = useState(null);
  const [isUploading, setIsUploading] = useState(false);
  const [activeTab, setActiveTab] = useState("overview");
  const [showProviderForm, setShowProviderForm] = useState(false);
  const [showEditForm, setShowEditForm] = useState(false);
  const [showServiceDropdown, setShowServiceDropdown] = useState(false);
  
  const fileInputRef = useRef(null);
  const navigate = useNavigate();

  const fetchUserProfile = () => {
    const token = localStorage.getItem("token");
    if (!token) {
      setLoading(false);
      return;
    }

    axios
      .get(`${API_BASE}/profile`, {
        headers: { Authorization: `Bearer ${token}` },
      })
      .then((res) => {
        if (res.data.success) {
          setUser({
            ...res.data.user,
            // Add these fields if they don't exist in your response
            rating: res.data.user.rating || 0,
            totalBookings: res.data.user.total_bookings || 0,
            totalReviews: res.data.user.total_reviews || 0,
            completedJobs: res.data.user.completed_jobs || 0,
            memberSince: new Date(res.data.user.created_at).toLocaleDateString(),
            address: res.data.user.location || "Not specified",
            details: res.data.user.bio || "No information provided.",
            phone: res.data.user.phone || "Not specified"
          });
        }
      })
      .catch((err) => {
        console.error(err);
        setError("Failed to fetch profile. Please login again.");
      })
      .finally(() => setLoading(false));
  };

  const fetchBookingHistory = async () => {
    const token = localStorage.getItem("token");
    if (!token) return;

    setLoadingBookings(true);
    try {
      const response = await axios.get(`${API_BASE}/bookings/history`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      
      if (response.data.success) {
        setBookingHistory(response.data.bookings || []);
      }
    } catch (err) {
      console.error("Error fetching booking history:", err);
      // Don't set error for booking history as it's not critical
    } finally {
      setLoadingBookings(false);
    }
  };

  // Check if user is admin
  const isAdmin = () => {
    return !!localStorage.getItem("admin_token");
  };


  useEffect(() => {
    fetchUserProfile();
    fetchBookingHistory();
    
    // Close dropdown when clicking outside
    const handleClickOutside = (event) => {
      if (showServiceDropdown && !event.target.closest('.servora-service-dropdown-container')) {
        setShowServiceDropdown(false);
      }
    };
    
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, [showServiceDropdown]);

  const handlePictureEdit = () => {
    fileInputRef.current.click();
  };

  const handleFileChange = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    setIsUploading(true);
    const formData = new FormData();
    formData.append("file", file);
    formData.append("upload_preset", "Appointment");

    try {
      // Upload image to Cloudinary
      const cloudinaryRes = await axios.post(
        "https://api.cloudinary.com/v1_1/djlfgopne/image/upload",
        formData
      );
      const profilePictureUrl = cloudinaryRes.data.secure_url;

      // Update profile picture URL in your backend
      const token = localStorage.getItem("token");
      await axios.put(
        `${API_BASE}/profile/update-picture`,
        { profile_picture: profilePictureUrl },
        { headers: { Authorization: `Bearer ${token}` } }
      );

      // Update state to show new picture immediately
      setUser((prevUser) => ({ ...prevUser, profile_picture: profilePictureUrl }));

    } catch (err) {
      console.error("Error uploading image:", err);
      setError("Failed to update profile picture. Please try again.");
    } finally {
      setIsUploading(false);
    }
  };

  const handleProviderApply = async (formData) => {
    const token = localStorage.getItem("token");
    if (!token) return;

    try {
      // First upload the document to Cloudinary
      let documentUrl = '';
      if (formData.documents) {
        const fileFormData = new FormData();
        fileFormData.append("file", formData.documents);
        fileFormData.append("upload_preset", "Appointment");

        const cloudinaryRes = await axios.post(
          "https://api.cloudinary.com/v1_1/djlfgopne/raw/upload",
          fileFormData
        );
        documentUrl = cloudinaryRes.data.secure_url;
      }

      // Then submit the application
      const response = await axios.post(
        `${API_BASE}/provider-applications`,
        {
          real_name: formData.real_name,
          document_url: documentUrl
        },
        { headers: { Authorization: `Bearer ${token}` } }
      );

      if (response.data.message) {
        // Update user state with application status
        setUser(prevUser => ({
          ...prevUser,
          application_status: 'pending'
        }));
        alert("Application submitted successfully! We'll review it soon.");
      }
    } catch (err) {
      console.error("Error submitting application:", err);
      
      if (err.response?.status === 409) {
        // Handle conflict - user already has pending application or is verified
        alert(err.response.data.message || "You already have a pending application or are already verified.");
      } else {
        setError("Failed to submit application. Please try again.");
      }
    } finally {
      setShowProviderForm(false);
    }
  };
  
  const handleProfileUpdate = async (formData) => {
    const token = localStorage.getItem("token");
    if (!token) return;

    try {
        // Make sure this URL matches exactly what's in your routes
        const response = await axios.put(
            `${API_BASE}/profile/update`,
            formData,
            { headers: { Authorization: `Bearer ${token}` } }
        );

        if (response.data.success) {
            // Update user state with new details
            setUser(prevUser => ({
                ...prevUser,
                name: formData.name,
                phone: formData.phone,
                location: formData.address,
                address: formData.address,
                bio: formData.details,
                details: formData.details
            }));
        }
    } catch (err) {
        console.error("Error updating profile:", err);
        setError("Failed to update profile. Please try again.");
    } finally {
        setShowEditForm(false);
    }
  };

  if (loading) return (
    <div className="servora-container" style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '50vh', flexDirection: 'column' }}>
      <ThreeDot color={["#1d547a", "#2771a3", "#318dcc", "#59a4d7"]} />
      <p style={{ marginTop: '20px', color: '#6c757d' }}>Loading profile...</p>
    </div>
  );
  if (error) return <div className="servora-container">Error: {error}</div>;
  if (!user) return <div className="servora-container">No profile data found. Please login.</div>;

  const isProvider = user.is_verified;
  const hasPendingApplication = user.application_status === 'pending';

  const tabs = [
    { key: "overview", label: "Overview", icon: UserIcon },
    { key: "appointments", label: "Appointments", icon: Calendar },
    ...(isProvider ? [{ key: "reviews", label: "Reviews", icon: Star }] : [])
  ];

  return (
    <div className="servora-profile">
      {/* Hero Section */}
      <div className="servora-hero">
        <div className="servora-hero-overlay" />
        <div className="servora-container servora-hero-content">
          <div className="servora-hero-left">
            <div className="servora-avatar-wrapper">
              <img
                className="servora-avatar-lg"
                src={user.profile_picture || defaultProfilePic}
                alt={user.name}
              />
              <button
                className="servora-edit-avatar-btn"
                onClick={handlePictureEdit}
                disabled={isUploading}
              >
                {isUploading ? "..." : <Pencil size={14} />}
              </button>
              <input
                type="file"
                ref={fileInputRef}
                onChange={handleFileChange}
                style={{ display: "none" }}
                accept="image/*"
              />
            </div>

            <div className="servora-hero-info">
              <div className="servora-name-row">
                <h1>{user.name}</h1>
                {isProvider && (
                  <span className="servora-verify-pill">
                    <ShieldCheck size={16} /> Verified Provider
                  </span>
                )}
              </div>
              
              {isProvider && (
                <div className="servora-rating-row">
                  <StarRow value={user.rating} />
                  <span className="servora-rating-text">
                    {user.rating} ({user.totalReviews} reviews)
                  </span>
                </div>
              )}
              
              <div className="servora-meta-row">
                <Pill icon={MapPin}>{user.address}</Pill>
                <Pill icon={Mail}>{user.email}</Pill>
                {user.phone && <Pill icon={Phone}>{user.phone}</Pill>}
              </div>
            </div>
          </div>
          
          <div className="servora-hero-right">
            
            <button 
              className="servora-btn servora-outline servora-lg" 
              onClick={() => setShowEditForm(true)}
            >
              <Edit size={16} /> Edit Profile
            </button>
            
            {!isProvider && !hasPendingApplication && !isAdmin() && (
              <button 
                className="servora-btn servora-primary servora-lg" 
                onClick={() => setShowProviderForm(true)}
              >
                Apply as Provider
              </button>
            )}
            
            {hasPendingApplication && (
              <div className="servora-application-status">
                <Clock size={16} /> Application under review
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Main Content */}
      <div className="servora-container servora-main-grid">
        <div className="servora-content">
          <Tabs tabs={tabs} active={activeTab} setActive={setActiveTab} />

          {activeTab === "overview" && (
            <section className="servora-card">
              <h3>About</h3>
              <p className="servora-lead">{user.details}</p>
              
              <div className="servora-info-grid">
                <div>
                  <span className="servora-label">Role</span>
                  <span className="servora-value">{isProvider ? "Service Provider" : "Customer"}</span>
                </div>
                <div>
                  <span className="servora-label">Email</span>
                  <span className="servora-value">{user.email}</span>
                </div>
                <div>
                  <span className="servora-label">Phone</span>
                  <span className="servora-value">{user.phone || "Not specified"}</span>
                </div>
                <div>
                  <span className="servora-label">Member Since</span>
                  <span className="servora-value">{user.memberSince}</span>
                </div>
                <div>
                  <span className="servora-label">Location</span>
                  <span className="servora-value">{user.address}</span>
                </div>
              </div>
              
              {isProvider && (
                <div className="servora-stats-grid">
                  <Stat icon={Calendar} label="Total Bookings" value={user.totalBookings} />
                  <Stat icon={CheckCircle} label="Completed Jobs" value={user.completedJobs} />
                  <Stat icon={Star} label="Reviews" value={user.totalReviews} />
                </div>
              )}
              
              {!isProvider && (
                <div className="servora-stats-grid">
                  <Stat icon={Calendar} label="Total Bookings" value={user.totalBookings} />
                </div>
              )}
            </section>
          )}

          {activeTab === "appointments" && (
            <section className="servora-card">
              <div className="servora-section-header">
                <h3>My Booking History</h3>
                <span className="servora-count-badge">
                  {bookingHistory.length} booking{bookingHistory.length !== 1 ? 's' : ''}
                </span>
              </div>
              
              {loadingBookings ? (
                <div className="servora-loading-state" style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', flexDirection: 'column', padding: '40px' }}>
                  <ThreeDot color={["#1d547a", "#2771a3", "#318dcc", "#59a4d7"]} />
                  <p style={{ marginTop: '20px', color: '#6c757d' }}>Loading booking history...</p>
                </div>
              ) : bookingHistory.length > 0 ? (
                <div className="servora-appointments-list">
                  {bookingHistory.map((booking) => (
                    <div key={booking.id} className="servora-appointment-item">
                      <div className="servora-appointment-header">
                        <div className="servora-appointment-service">
                          <h4>{booking.service?.name || 'Service'}</h4>
                          <p className="servora-service-category">
                            {booking.service?.category || 'General Service'}
                          </p>
                        </div>
                        <span className={`servora-status servora-status-${booking.status?.toLowerCase() || 'pending'}`}>
                          {booking.status || 'Pending'}
                        </span>
                      </div>
                      
                      <div className="servora-appointment-details">
                        <div className="servora-detail-item">
                          <Clock size={16} />
                          <span>
                            {booking.booking_time 
                              ? new Date(booking.booking_time).toLocaleString('en-US', {
                                  year: 'numeric',
                                  month: 'short',
                                  day: 'numeric',
                                  hour: '2-digit',
                                  minute: '2-digit'
                                })
                              : 'Time not specified'
                            }
                          </span>
                        </div>
                        
                        {booking.service?.user?.name && (
                          <div className="servora-detail-item">
                            <UserIcon size={16} />
                            <span>Provider: {booking.service.user.name}</span>
                          </div>
                        )}
                        
                        {booking.service?.location && (
                          <div className="servora-detail-item">
                            <MapPin size={16} />
                            <span>{booking.service.location}</span>
                          </div>
                        )}
                        
                        {booking.service?.price && (
                          <div className="servora-detail-item">
                            <span className="servora-price-label">💰</span>
                            <span>Price: {booking.service.price} BDT</span>
                          </div>
                        )}
                        
                        <div className="servora-detail-item">
                          <span className="servora-payment-label">💳</span>
                          <span className={`servora-payment-status servora-payment-${booking.payment_status?.toLowerCase() || 'pending'}`}>
                            Payment: {booking.payment_status || 'Pending'}
                          </span>
                        </div>
                      </div>
                      
                      {booking.service?.description && (
                        <div className="servora-appointment-description">
                          <p>{booking.service.description}</p>
                        </div>
                      )}
                    </div>
                  ))}
                </div>
              ) : (
                <div className="servora-empty-state">
                  <Calendar size={48} />
                  <h4>No Booking History</h4>
                  <p>You haven't made any bookings yet. Start by browsing available services.</p>
                  <button 
                    className="servora-btn servora-primary"
                    onClick={() => navigate('/homepage')}
                  >
                    Browse Services
                  </button>
                </div>
              )}
            </section>
          )}

          {activeTab === "reviews" && isProvider && (
            <section className="servora-card">
              <div className="servora-reviews-head">
                <h3>Customer Reviews</h3>
                <div className="servora-score">
                  <div className="servora-big">{user.rating}</div>
                  <StarRow value={user.rating} />
                  <div className="servora-muted">{user.totalReviews} reviews</div>
                </div>
              </div>
              {user.reviews?.length > 0 ? (
                <div className="servora-reviews-list">
                  {user.reviews.map((review) => (
                    <ReviewItem key={review.id} r={review} />
                  ))}
                </div>
              ) : (
                <div className="servora-empty-state">
                  <Star size={48} />
                  <p>No reviews yet.</p>
                </div>
              )}
            </section>
          )}
        </div>

        {/* Sidebar */}
        <aside className="servora-sidebar">
          <div className="servora-side-card">
            <h4>Contact Information</h4>
            <div className="servora-contact-row">
              <MapPin size={16} />
              <span>{user.address}</span>
            </div>
            {user.phone && (
              <div className="servora-contact-row">
                <Phone size={16} />
                <span>{user.phone}</span>
              </div>
            )}
            <div className="servora-contact-row">
              <Mail size={16} />
              <span>{user.email}</span>
            </div>
          </div>

          <div className="servora-side-card">
            <h4>Account Summary</h4>
            <div className="servora-summary-list">
              <div className="servora-summary-item">
                <span className="servora-summary-label">Status</span>
                <span className="servora-summary-value">
                  {isProvider ? "Service Provider" : "Customer"}
                </span>
              </div>
              <div className="servora-summary-item">
                <span className="servora-summary-label">Member Since</span>
                <span className="servora-summary-value">{user.memberSince}</span>
              </div>
              <div className="servora-summary-item">
                <span className="servora-summary-label">Total Bookings</span>
                <span className="servora-summary-value">{user.totalBookings}</span>
              </div>
              {isProvider && (
                <div className="servora-summary-item">
                  <span className="servora-summary-label">Completed Jobs</span>
                  <span className="servora-summary-value">{user.completedJobs}</span>
                </div>
              )}
            </div>
          </div>
        </aside>
      </div>

      {/* Provider Application Modal */}
      {showProviderForm && (
        <ProviderForm 
          onClose={() => setShowProviderForm(false)} 
          onSubmit={handleProviderApply}
        />
      )}
      
      {/* Edit Profile Modal */}
      {showEditForm && (
        <EditProfileForm 
          user={user}
          onClose={() => setShowEditForm(false)} 
          onSubmit={handleProfileUpdate}
        />
      )}
    </div>
  );
};

export default ProfilePage;
