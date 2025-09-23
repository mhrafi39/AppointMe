import React from 'react';
import { FaUsers, FaLightbulb, FaHandshake } from 'react-icons/fa';
import '../styles/AboutUs.css';

const AboutUs = () => {


  const values = [
    {
      icon: <FaUsers />,
      title: "Community First",
      description: "We believe in building strong connections within local communities by providing accessible, reliable services."
    },
    {
      icon: <FaLightbulb />,
      title: "Innovation",
      description: "Continuously improving our platform with cutting-edge technology to enhance user experience."
    },
    {
      icon: <FaHandshake />,
      title: "Trust & Reliability",
      description: "Building lasting relationships through transparent communication and consistent service delivery."
    }
  ];

  return (
    <div className="about-us-container">
      {/* Hero Section */}
      <section className="about-hero">
        <div className="hero-content">
          <h1 className="hero-title">About AppointMe</h1>
          <p className="hero-subtitle">
            Connecting communities through exceptional service experiences
          </p>
          <div className="hero-decoration"></div>
        </div>
      </section>

      {/* Mission Section */}
      <section className="mission-section">
        <div className="container">
          <div className="mission-content">
            <div className="mission-text">
              <h2>Our Mission</h2>
              <p>
                At AppointMe, we're dedicated to revolutionizing how communities access and deliver services. 
                Our platform bridges the gap between service providers and customers, creating a seamless 
                ecosystem where quality meets convenience.
              </p>
              <p>
                We believe that everyone deserves access to reliable, professional services in their 
                local area. Through our innovative platform, we're making it easier than ever to find 
                trusted providers and book services with confidence.
              </p>
            </div>
            <div className="mission-visual">
              <div className="floating-card">
                <div className="card-content">
                  <h3>50,000+</h3>
                  <p>Happy Customers</p>
                </div>
              </div>
              <div className="floating-card">
                <div className="card-content">
                  <h3>5,000+</h3>
                  <p>Service Providers</p>
                </div>
              </div>
              <div className="floating-card">
                <div className="card-content">
                  <h3>99.9%</h3>
                  <p>Uptime</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Values Section */}
      <section className="values-section">
        <div className="container">
          <h2 className="section-title">Our Core Values</h2>
          <div className="values-grid">
            {values.map((value, index) => (
              <div key={index} className="value-card">
                <div className="value-icon">{value.icon}</div>
                <h3>{value.title}</h3>
                <p>{value.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Story Section */}
      <section className="story-section">
        <div className="container">
          <div className="story-content">
            <h2>Our Story</h2>
            <div className="story-timeline">
              <div className="timeline-item">
                <div className="timeline-year">2023</div>
                <div className="timeline-content">
                  <h3>The Beginning</h3>
                  <p>AppointMe was founded with a simple vision: to make quality services accessible to everyone in their local community.</p>
                </div>
              </div>
              <div className="timeline-item">
                <div className="timeline-year">2024</div>
                <div className="timeline-content">
                  <h3>Rapid Growth</h3>
                  <p>Within our first year, we've connected thousands of service providers with satisfied customers across multiple cities.</p>
                </div>
              </div>
              <div className="timeline-item">
                <div className="timeline-year">2025</div>
                <div className="timeline-content">
                  <h3>Innovation Continues</h3>
                  <p>Today, we're expanding our platform with AI-powered matching and enhanced user experiences.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
};

export default AboutUs;