import React, { useState } from 'react';
import axios from 'axios';
import '../styles/ApplicationModal.css';
import { API_BASE } from '../api';

const ApplicationModal = ({ user, onClose, onApplicationSuccess }) => {
  const [realName, setRealName] = useState('');
  const [documentFile, setDocumentFile] = useState(null);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [error, setError] = useState('');

  const handleFileChange = (event) => {
    setDocumentFile(event.target.files[0]);
  };

  const handleSubmit = async (event) => {
    event.preventDefault();
    if (!realName || !documentFile) {
      setError('Please fill in all fields and attach your document.');
      return;
    }
    setError('');
    setIsSubmitting(true);

    try {
      // 1. Upload document to Cloudinary
      const formData = new FormData();
      formData.append('file', documentFile);
      formData.append('upload_preset', 'Appointment'); // Use your Cloudinary upload preset

      const cloudinaryRes = await axios.post(
        'https://api.cloudinary.com/v1_1/djlfgopne/image/upload', // Use your Cloudinary cloud name
        formData
      );
      const documentUrl = cloudinaryRes.data.secure_url;

      // 2. Submit application to your backend
      const token = localStorage.getItem('token');
      await axios.post(
        `${API_BASE}/provider-applications`,
        {
          real_name: realName,
          document_url: documentUrl,
        },
        { headers: { Authorization: `Bearer ${token}` } }
      );

      alert('Application submitted successfully!');
      onApplicationSuccess(); // This will update the user state in ProfilePage
      onClose(); // Close the modal
    } catch (err) {
      console.error('Application submission error:', err);
      const message = err.response?.data?.message || 'An error occurred during submission.';
      setError(message);
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="modal-overlay">
      <div className="modal-content">
        <button className="modal-close-button" onClick={onClose} disabled={isSubmitting}>&times;</button>
        <h2>Apply to be a Provider</h2>
        <p>Please provide your legal name and a photo of your NID or Passport for verification.</p>
        <form onSubmit={handleSubmit}>
          <div className="form-group">
            <label htmlFor="realName">Full Legal Name</label>
            <input
              type="text"
              id="realName"
              value={realName}
              onChange={(e) => setRealName(e.target.value)}
              required
            />
          </div>
          <div className="form-group">
            <label htmlFor="document">NID / Passport</label>
            <input
              type="file"
              id="document"
              onChange={handleFileChange}
              accept="image/*,.pdf"
              required
            />
          </div>
          {error && <p className="error-message">{error}</p>}
          <button type="submit" className="submit-button" disabled={isSubmitting}>
            {isSubmitting ? 'Submitting...' : 'Submit Application'}
          </button>
        </form>
      </div>
    </div>
  );
};

export default ApplicationModal;