import React from "react";
import { BrowserRouter as Router, Routes, Route, useLocation } from "react-router-dom";
import Header from "./components/Header"; // Make sure Header is imported

import LandingPage from "./pages/LandingPage";
import LoginPage from "./pages/LoginPage";
import SignupPage from "./pages/SignupPage";
import ServiceForm from "./pages/ServiceForm";
import HomePage from "./pages/HomePage";
import ServicePage from "./pages/ServicePage";
import PaymentPage from "./pages/PaymentPage";
import Profile from "./pages/ProfilePage";
import HomePageAdmin from "./pages/HomePageAdmin";
import AboutUs from "./pages/AboutUs";

import "./App.css";
import AdminProfile from "./pages/AdminProfile";
import BookingHistory from "./pages/BookingHistory";
import Contact from "./pages/Contact";
import AdminDashboard from "./components/AdminDashboard";
import ChatbotWidget from "./components/ChatbotWidget";
import SettingsPage from "./pages/SettingsPage";

import DeleteServices from "./pages/DeleteServices";
import DeleteMyServices from "./pages/DeleteMyServices";
import NotificationsPage from "./components/NotificationPage";
import Footer from "./components/Footer";

import ServiceBooked from "./pages/ServiceBooked";


// Component to conditionally render footer
function ConditionalFooter() {
  const location = useLocation();
  const hideFooterPaths = ['/login', '/signup'];
  
  if (hideFooterPaths.includes(location.pathname)) {
    return null;
  }
  
  return <Footer />;
}


function App() {
  return (
    <Router>
      <Header /> {/* This should be at the top */}
      <ChatbotWidget/>
      <main>
        <Routes>
          {/* Public Routes */}
          <Route path="/" element={<LandingPage />} />      
          <Route path="/login" element={<LoginPage />} />   
          <Route path="/signup" element={<SignupPage />} /> 
          <Route path="/about" element={<AboutUs />} />

          {/* User Routes */}
          <Route path="/homepage" element={<HomePage />} /> 
          <Route path="/service-form" element={<ServiceForm />} />
          <Route path="/service/:id" element={<ServicePage />} />
          <Route path="/payment/:bookingId" element={<PaymentPage />} />
          <Route path="/profile" element={<Profile />} />

         <Route path="/notifications" element={<NotificationsPage />} />

          {/* Admin Routes */}
          <Route path="/admin-dashboard" element={<AdminDashboard />} />
          <Route path="/homepage-admin" element={<HomePageAdmin />} />

          <Route path="/admin-profile" element={<AdminProfile />} />

          <Route path="/booking-history" element={<BookingHistory />} />

          {/* Settings Route */}
          <Route path="/settings" element={<SettingsPage />} />
          {/* Fallback Route */}
          <Route
            path="*"
            element={<h2 className="text-center mt-10">Page Not Found</h2>}
          />

           {/* Contact Route */}
          <Route path="/Contact" element={<Contact />} />

          <Route path="/delete-service" element={<DeleteServices/>} />
          <Route path="/delete-my-service" element={<DeleteMyServices/>} />

          <Route path="/services-booked" element={<ServiceBooked />} />

        </Routes>
        <ConditionalFooter/>
      </main>
    </Router>
  );
}

export default App;
