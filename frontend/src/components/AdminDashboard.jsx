import React, { useEffect, useState } from "react";
import axios from "axios";
import { ThreeDot } from "react-loading-indicators";
import "../styles/AdminDashboard.css";
import { API_BASE } from "../api";

const AdminDashboard = () => {
  const [applications, setApplications] = useState([]);
  const [loading, setLoading] = useState(true);
  const [actionLoading, setActionLoading] = useState({});
  const [error, setError] = useState('');
  const [successMessage, setSuccessMessage] = useState('');
  const [stats, setStats] = useState({
    totalApplications: 0,
    pendingApplications: 0,
    approvedToday: 0
  });

  // Fetch pending applications
  const fetchPendingApplications = async () => {
    setLoading(true);
    setError('');
    try {
      const token = localStorage.getItem("admin_token");
      if (!token) {
        setError("No admin token found. Please login as admin.");
        return;
      }

      const res = await axios.get(`${API_BASE}/admin/applications`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      
      const applicationsData = res.data.applications || [];
      setApplications(applicationsData);
      
      // Update stats
      setStats({
        totalApplications: applicationsData.length,
        pendingApplications: applicationsData.filter(app => app.status === 'pending').length,
        approvedToday: applicationsData.filter(app => 
          app.status === 'approved' && 
          new Date(app.updated_at).toDateString() === new Date().toDateString()
        ).length
      });
      
    } catch (error) {
      console.error("Error fetching applications", error);
      if (error.response?.status === 401) {
        setError("Session expired. Please login again as admin.");
        localStorage.removeItem("admin_token");
      } else {
        setError("Failed to fetch applications. Please check your connection.");
      }
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchPendingApplications();
  }, []);

  const handleApprove = async (id) => {
    setActionLoading(prev => ({ ...prev, [id]: 'approving' }));
    setError('');
    setSuccessMessage('');
    
    try {
      const token = localStorage.getItem("admin_token");
      await axios.post(`${API_BASE}/admin/applications/${id}/approve`, {}, {
        headers: { Authorization: `Bearer ${token}` },
      });
      
      setSuccessMessage("Application approved successfully!");
      fetchPendingApplications();
      
      // Clear success message after 3 seconds
      setTimeout(() => setSuccessMessage(''), 3000);
    } catch (error) {
      console.error("Error approving application", error);
      setError("Failed to approve application. Please try again.");
    } finally {
      setActionLoading(prev => ({ ...prev, [id]: null }));
    }
  };

  const handleReject = async (id) => {
    setActionLoading(prev => ({ ...prev, [id]: 'rejecting' }));
    setError('');
    setSuccessMessage('');
    
    try {
      const token = localStorage.getItem("admin_token");
      await axios.post(`${API_BASE}/admin/applications/${id}/reject`, {}, {
        headers: { Authorization: `Bearer ${token}` },
      });
      
      setSuccessMessage("Application rejected successfully!");
      fetchPendingApplications();
      
      // Clear success message after 3 seconds
      setTimeout(() => setSuccessMessage(''), 3000);
    } catch (error) {
      console.error("Error rejecting application", error);
      setError("Failed to reject application. Please try again.");
    } finally {
      setActionLoading(prev => ({ ...prev, [id]: null }));
    }
  };

  if (loading) {
    return (
      <div className="admin-dashboard-container">
        <div className="loading-container">
          <ThreeDot color={["#1d547a", "#2771a3", "#318dcc", "#59a4d7"]} />
          <h2>Loading Applications...</h2>
          <p>Please wait while we fetch the data</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="admin-dashboard-container">
        <div className="error-container">
          <div className="error-icon">❌</div>
          <h2>Error Loading Applications</h2>
          <p className="error-message">{error}</p>
          <button 
            className="btn-primary" 
            onClick={fetchPendingApplications}
          >
            Retry
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="admin-dashboard-container">
      {/* Header Section */}
      <div className="dashboard-header">
        <div className="header-content">
          <h1>Provider Applications Dashboard</h1>
          <p>Manage and review provider applications</p>
        </div>
        <button 
          className="btn-refresh" 
          onClick={fetchPendingApplications}
          disabled={loading}
        >
          🔄 Refresh
        </button>
      </div>

      {/* Notifications */}
      {successMessage && (
        <div className="notification success">
          ✅ {successMessage}
        </div>
      )}
      
      {error && (
        <div className="notification error">
          ❌ {error}
        </div>
      )}

      {/* Stats Cards */}
      <div className="stats-container">
        <div className="stat-card">
          <div className="stat-icon">📋</div>
          <div className="stat-content">
            <h3>{stats.totalApplications}</h3>
            <p>Total Applications</p>
          </div>
        </div>
        <div className="stat-card">
          <div className="stat-icon">⏳</div>
          <div className="stat-content">
            <h3>{stats.pendingApplications}</h3>
            <p>Pending Review</p>
          </div>
        </div>
        <div className="stat-card">
          <div className="stat-icon">✅</div>
          <div className="stat-content">
            <h3>{stats.approvedToday}</h3>
            <p>Approved Today</p>
          </div>
        </div>
      </div>

      {/* Applications Section */}
      <div className="applications-section">
        <div className="section-header">
          <h2>Pending Applications</h2>
          <span className="application-count">
            {applications.length} application{applications.length !== 1 ? 's' : ''}
          </span>
        </div>

        {applications.length > 0 ? (
          <div className="applications-grid">
            {applications.map((app) => (
              <div key={app.id} className="application-card">
                <div className="card-header">
                  <div className="applicant-info">
                    <h3>{app.user.name}</h3>
                    <p className="email">{app.user.email}</p>
                  </div>
                  <div className="application-date">
                    {new Date(app.created_at).toLocaleDateString()}
                  </div>
                </div>

                <div className="card-content">
                  <div className="info-row">
                    <span className="label">Legal Name:</span>
                    <span className="value">{app.real_name}</span>
                  </div>
                  
                  <div className="info-row">
                    <span className="label">Document:</span>
                    <a 
                      href={app.document_url} 
                      target="_blank" 
                      rel="noopener noreferrer" 
                      className="document-link"
                    >
                      📄 View Document
                    </a>
                  </div>

                  <div className="info-row">
                    <span className="label">Applied:</span>
                    <span className="value">
                      {new Date(app.created_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                      })}
                    </span>
                  </div>
                </div>

                <div className="card-actions">
                  <button 
                    className="btn-approve" 
                    onClick={() => handleApprove(app.id)}
                    disabled={actionLoading[app.id]}
                  >
                    {actionLoading[app.id] === 'approving' ? (
                      <>
                        <ThreeDot size="small" color={["#10b981"]} />
                        Approving...
                      </>
                    ) : (
                      <>
                        ✅ Approve
                      </>
                    )}
                  </button>
                  
                  <button 
                    className="btn-reject" 
                    onClick={() => handleReject(app.id)}
                    disabled={actionLoading[app.id]}
                  >
                    {actionLoading[app.id] === 'rejecting' ? (
                      <>
                        <ThreeDot size="small" color={["#ef4444"]} />
                        Rejecting...
                      </>
                    ) : (
                      <>
                        ❌ Reject
                      </>
                    )}
                  </button>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div className="empty-state">
            <div className="empty-icon">📭</div>
            <h3>No Pending Applications</h3>
            <p>All applications have been reviewed or no new applications have been submitted.</p>
            <button 
              className="btn-primary" 
              onClick={fetchPendingApplications}
            >
              Check for New Applications
            </button>
          </div>
        )}
      </div>
    </div>
  );
};

export default AdminDashboard;
