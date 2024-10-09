import axios from 'axios';

// 創建一個 axios 實例並設置 baseURL
const api = axios.create({
    baseURL: 'http://127.0.0.1:8000/api', // 基礎 URL
});

export default api;
