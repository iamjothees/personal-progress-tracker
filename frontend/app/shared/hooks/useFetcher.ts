import { useState, useEffect } from 'react';
import axios, { AxiosError, AxiosResponse } from 'axios';
import useApi from '@/core/http/useApi';

interface Config {
    url: string;
    method?: 'get' | 'post' | 'put' | 'delete' | null;
    data?: object;
    params?: object;
    baseUrl?: string;
}

export default function useFetcher( config: Config ) {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(null);
    const [error, setError] = useState(null);

    const api = useApi();

    useEffect(() => {
        setLoading(true);
        setError(null);
        setData(null);

        if (Boolean(config.method) === false) {
            config.method = 'get';
        }

        if (config.baseUrl){
            const fetcher = axios.create({
                baseURL: config.baseUrl,
                timeout: 5000,
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            });

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

                fetcher({ ...config, })
                    .then((res: AxiosResponse) => {
                        setData(res.data);
                    })
                    .catch((err: AxiosError) => {
                        setError(err);
                    })
                    .finally(() => {
                        setLoading(false);
                    });
        }else{
            setData(() => api.data);
            setError(() => api.error);
            setLoading(() => api.loading);

            switch (config.method) {
                case 'get':
                    api.get(config.url, config.params);
                    break;
            
                case 'post':
                    api.post(config.url, config.data, config.params);
                    break;
            
                case 'put':
                    api.put(config.url, config.data, config.params);
                    break;
            
                case 'delete':
                    api.del(config.url, config.params);
                    break;
            
                default:
                    break;
            }

        }
    }, []);

    useEffect(() => {
        if (config.baseUrl) return;

        setData(api.data);
        setError(api.error);
        setLoading(api.loading);
    }, [api.data, api.loading, api.error]);

    return { data, loading, error };
}