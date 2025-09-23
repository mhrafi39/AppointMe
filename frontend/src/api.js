import axios from "axios";

export const API_BASE = "http://localhost:8000/api";
export const STORAGE_BASE = "http://localhost:8000/storage";

export const getToken = () => {
  const userToken = localStorage.getItem("token");
  const adminToken = localStorage.getItem("admin_token");
  return userToken || adminToken;
};

export const isAdmin = () => !!localStorage.getItem("admin_token");

export const authHeaders = () => {
  const token = getToken();
  return {
    "Content-Type": "application/json",
    Authorization: token ? `Bearer ${token}` : "",
  };
};

export const fetchProfile = async () => {
  const token = getToken();
  if (!token) return null;

  const url = isAdmin()
    ? `${API_BASE}/admin/profile`
    : `${API_BASE}/profile`;

  try {
    const res = await axios.get(url, { headers: authHeaders() });
    return res.data.user || res.data.admin || null;
  } catch (err) {
    if (err.response && err.response.status === 401) {
      localStorage.removeItem(isAdmin() ? "admin_token" : "token");
    }
    return null;
  }
};
