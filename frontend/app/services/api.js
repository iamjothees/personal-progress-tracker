import axios from 'axios';

export default axios.create({
    baseURL: import.meta.env.VITE_APP_API_URL,
    timeout: 5000,
    headers: { 
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    withCredentials: true,
    withXSRFToken: true
});