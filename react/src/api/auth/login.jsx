import axios from '../../axios';

export const login = async (email, password) => {
    const response = await axios.post(`/login`, { email, password });
    return response.data;
  };