import { useState, useEffect } from 'react';
import api from '@/services/api';
import axios, { AxiosInstance, AxiosError, AxiosResponse, Method } from 'axios';

interface Config {
    url: string;
    method?: Method;
    data?: object;
    params?: object;
    baseUrl?: string;
}

export default function useFetcher(config: Config, apiAxios: AxiosInstance = null ) {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(null);
    const [error, setError] = useState(null);

    useEffect(() => {
        setData(null);
        setError(null);
        setLoading(true);

        if (apiAxios === null) {
            apiAxios = api;
        }

        if (config.baseUrl) {
            apiAxios.defaults.baseURL = config.baseUrl;
        }

        if (config.method === null) {
            config.method = 'get';
        }

        switch (config.method) {
            case 'get':
                if (config.data){
                    config.params = { ...config.data, ...(config.params || {})};
                }
                break;
        
            default:
                if (config.params) {
                    config.data = { ...config.params, ...(config.data || {})};
                }
                break;
        }

            apiAxios({ ...config, })
                .then((res: AxiosResponse) => {
                    setData(res.data);
                })
                .catch((err: AxiosError) => {
                    setError(err);
                })
                .finally(() => {
                    setLoading(false);
                });

    }, []);

    return { data, loading, error };
}