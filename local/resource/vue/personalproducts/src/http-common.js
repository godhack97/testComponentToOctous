import axios from 'axios';

export const HTTP = axios.create({
   baseURL: 'https://sodamoda.ru/',
   headers: {
       'Authorization-Token': 'b977f556-5443436b-99fe3642-37c055f5',
   }
});