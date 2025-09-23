import React from "react";
import "../styles/ServiceCard.css"; // import the CSS file
import { STORAGE_BASE } from "../api";

import EventImage from "../assets/images/event.jpeg";
import LImg from "../assets/images/L_img.png";

const ServiceCard = ({ service }) => {
  const profileImg =
    service.profile_picture && service.profile_picture !== ""
      ? service.profile_picture
      : `${STORAGE_BASE}/default.jpeg`; // fallback

  const toolsIcon = `${STORAGE_BASE}/tools.png`; // fixed working path

  
  return (
    <div className="service-card">
      <div className="service-img">
        <img src={EventImage} alt={service.title || "Service"} />
      </div>

      <div className="service-info">
        <div className="service-header">
          <div>
            <h4>{service.title}</h4>
            <p className="user_id">User-Id: {service.user_id}</p>
            <p className="job">Job: {service.category}</p>
          </div>

          <div className="price-section">
            <img src={LImg} alt="L" className="icon" />
            <p className="price">{service.price} Tk</p>
          </div>
        </div>

        <div className="service-footer">
          <div className="time">⏰ {service.available_time}</div>
        </div>
      </div>
    </div>
  );
};

export default ServiceCard;
