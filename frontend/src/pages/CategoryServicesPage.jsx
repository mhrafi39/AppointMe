import React, { useEffect, useState } from "react";
import { useParams, Link } from "react-router-dom";
import axios from "axios";
import ServiceCard from "../components/ServiceCard";   // ✅ import this
import { API_BASE } from "../api";

const CategoryServicesPage = () => {
  const { category } = useParams();
  const [services, setServices] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    if (!category) return;

    const fetchServices = async () => {
      try {
        setLoading(true);
        const res = await axios.get(
          `${API_BASE}/services/category/${encodeURIComponent(category)}`
        );
        setServices(res.data.data || []);
      } catch (err) {
        setError(
          err.response?.data?.message || "Failed to fetch category services"
        );
      } finally {
        setLoading(false);
      }
    };

    fetchServices();
  }, [category]);

  if (loading) {
    return (
      <div className="flex justify-center items-center min-h-screen">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="flex justify-center items-center min-h-screen">
        <div className="text-red-600">Error: {error}</div>
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-3xl font-bold mb-6 capitalize">
        {category} Services
      </h1>

      {services.length === 0 ? (
        <div className="text-center text-gray-600">No services found</div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {services.map((service) =>
            service.is_booked ? (
              <div
                key={service.services_id}
                className="service-card-link relative opacity-60"
              >
                <ServiceCard service={service} />
                <div className="absolute inset-0 bg-black/40 flex items-center justify-center text-white text-lg font-semibold">
                  Unavailable
                </div>
              </div>
            ) : (
              <Link
                key={service.services_id}
                to={`/service/${service.services_id}`}
                className="service-card-link"
              >
                <ServiceCard service={service} />
              </Link>
            )
          )}
        </div>
      )}
    </div>
  );
};

export default CategoryServicesPage;
