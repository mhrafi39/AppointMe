import React from "react";
import {
  Mail,
  Phone,
  MapPin,
  Facebook,
  Instagram,
  Youtube,
  Linkedin,
  Globe,
  ShieldCheck,
  ArrowRight,
  Store,
  Wrench,
  Handshake,
  MessageSquare
} from "lucide-react";
import "../styles/Footer.css";

const links = {
  company: [
    { label: "About AppointMe", href: "/about" },
    { label: "How it Works", href: "/how-it-works" },
    { label: "Careers", href: "/careers" },
    { label: "Press", href: "/press" },
    { label: "Contact", href: "/contact" },
  ],
  services: [
    { label: "Home Cleaning", href: "/services/home-cleaning" },
    { label: "AC Servicing", href: "/services/ac-servicing" },
    { label: "Electrical Works", href: "/services/electrical" },
    { label: "Plumbing", href: "/services/plumbing" },
    { label: "Beauty & Grooming", href: "/services/beauty" },
    { label: "Appliance Repair", href: "/services/appliance-repair" },
  ],
  support: [
    { label: "Help Center", href: "/help" },
    { label: "Order Tracking", href: "/orders" },
    { label: "Refund Policy", href: "/refund-policy" },
    { label: "Terms of Service", href: "/terms" },
    { label: "Privacy Policy", href: "/privacy" },
  ],
};

function AppBadge({ platform }) {
  return (
    <a href="#" className="app-badge">
      <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
        <path
          d="M12 2l3 5 6 .9-4.5 4 1 6L12 15l-5.5 2.9 1-6L3 7.9 9 7l3-5z"
          fill="currentColor"
        />
      </svg>
      <div>
        <div className="badge-sub">GET IT ON</div>
        <div className="badge-main">{platform}</div>
      </div>
    </a>
  );
}

function SocialIcon({ href, label, children }) {
  return (
    <a href={href} aria-label={label} className="social-icon">
      {children}
    </a>
  );
}

export default function Footer() {
  return (
    <footer className="footer">
      {/* CTA */}
      <div className="cta-box">
        <div>
          <h2>Quality home services, on demand.</h2>
          <p>
            Book verified professionals with transparent pricing and trusted
            support.
          </p>
        </div>
        <div className="cta-apps">
          <AppBadge platform="Google Play" />
          <AppBadge platform="App Store" />
        </div>
      </div>

      <div className="footer-grid">
        {/* Brand */}
        <div>
          <div className="brand">
            <div className="brand-icon">
              <Handshake size={20} />
            </div>
            <span className="brand-name">AppointMe</span>
          </div>
          <p className="brand-desc">
            A trusted marketplace for home services in Bangladesh. Compare,
            book, and relax — we handle the rest.
          </p>

          <div className="contact-list">
            <div>
              <MapPin size={14} />
              Dhaka, Bangladesh
            </div>
            <a href="tel:+8801000000000">
              <Phone size={14} /> +880 1000-000000
            </a>
            <a href="mailto:support@appointme.com">
              <Mail size={14} /> support@appointme.com
            </a>
          </div>

          <div className="trust-badges">
            <span>
              <ShieldCheck size={14} /> Verified Providers
            </span>
            <span>
              <Wrench size={14} /> 200+ Services
            </span>
            <span>
              <Store size={14} /> Citywide Coverage
            </span>
          </div>
        </div>

        {/* Links */}
        <div className="link-col">
          <h3>Company</h3>
          <ul>
            {links.company.map((l) => (
              <li key={l.label}>
                <a href={l.href}>{l.label}</a>
              </li>
            ))}
          </ul>
        </div>
        <div className="link-col">
          <h3>Popular Services</h3>
          <ul>
            {links.services.map((l) => (
              <li key={l.label}>
                <a href={l.href}>{l.label}</a>
              </li>
            ))}
          </ul>
        </div>
        <div className="link-col">
          <h3>Support</h3>
          <ul>
            {links.support.map((l) => (
              <li key={l.label}>
                <a href={l.href}>{l.label}</a>
              </li>
            ))}
          </ul>
        </div>
      </div>

      {/* Bottom */}
      <div className="footer-bottom">
        <div className="socials">
          <SocialIcon href="#" label="Facebook">
            <Facebook size={18} />
          </SocialIcon>
          <SocialIcon href="#" label="Instagram">
            <Instagram size={18} />
          </SocialIcon>
          <SocialIcon href="#" label="YouTube">
            <Youtube size={18} />
          </SocialIcon>
          <SocialIcon href="#" label="LinkedIn">
            <Linkedin size={18} />
          </SocialIcon>
        </div>
        <div className="bottom-links">
          <a href="/privacy">Privacy Policy</a>
          <span>•</span>
          <a href="/terms">Terms of Service</a>
          <span>•</span>
          <a href="/pricing">Pricing</a>
        </div>
      </div>
      <div className="copyright">
        © {new Date().getFullYear()} AppointMe Ltd. All rights reserved.
      </div>
    </footer>
  );
}