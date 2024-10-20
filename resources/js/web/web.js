import axios from 'axios';

// 創建一個 axios 實例並設置 baseURL
const web = axios.create({
    baseURL: 'http://127.0.0.1:8000', // 基礎 URL
    headers: {
        'X-Requested-With': 'XMLHttpRequest',  //標識 AJAX 請求
        'X-CSRF-TOKEN': document.querySelector("meta[name='csrf-token']").content, // 自動帶入 CSRF token
    },
    withCredentials: true, // 確保能攜帶 Cookie
});

export default web;
