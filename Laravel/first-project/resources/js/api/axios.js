import axios from 'axios';
import Toast from "../components/Toast";
import {setItem, getItem} from '../utils/localStorage';

const baseURL = '/api/';


const instance = axios.create();

instance.defaults.timeout = 30000; // 所有接口30s超时

// 请求统一处理
instance.interceptors.request.use(async config => {
  if (config.url && config.url.charAt(0) === '/') {
    config.url = `${baseURL}${config.url}`;
  }

  config.headers.authorization = `Bearer ${getItem('token')}`;
  config.headers.common['X-Requested-With'] = 'XMLHttpRequest';
  // 加入 CSRF Token 请求头
  let token = document.head.querySelector('meta[name="csrf-token"]');
  if (token) {
    config.headers.common['X-CSRF-TOKEN'] = token.content;
  } else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
  }

  return config;
}, error => Promise.reject(error));

// 对返回的内容做统一处理
instance.interceptors.response.use(response => {
  if (response.status === 200) {
    return response;
  }
  return Promise.reject(response);
}, error => {
  if (error) {
    let msg = '';
    if (error.response && error.response.status === 401) {
      msg = error.response.data.msg;
      return;
    }
    msg = '网络异常，请检查你的网络。';
    Toast({
      content: msg,
      timeout: 2000,
      background: "#f44336"
    });
  } else {
    Toast({
      content: '未知错误。',
      timeout: 2000,
      background: "#f44336"
    });
  }
  return Promise.reject(error);
});

export default instance;
