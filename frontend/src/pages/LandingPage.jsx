import React from "react";
import { useNavigate } from "react-router-dom"; // ✅ Navigation hook
import "../styles/LandingPage.css";
import bgShapes from "../assets/abstract-shapes.png"; // Make sure this file exists

const LandingPage = () => {
  const navigate = useNavigate(); // ✅ Navigation hook

  return (
    <div className="landing-container">
      {/* Background shapes */}
      <div
        className="background-shapes"
        style={{ backgroundImage: `url(${bgShapes})` }}
      ></div>

      {/* Main content */}
      <div className="landing-content">
        <h1>Professional Appointment Booking</h1>
        <p>Schedule medical appointments with ease. Connect with healthcare providers anytime, anywhere.</p>
        
        <button 
          className="cta-button" 
          onClick={() => navigate("/signup")}
        >
          Book Appointment
        </button>
      </div>
    </div>
  );
};

export default LandingPage;
