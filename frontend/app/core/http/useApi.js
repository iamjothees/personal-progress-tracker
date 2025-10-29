import { useState, useCallback, useEffect } from 'react';
import api from './api'; // Assuming api.js is in the same directory

const useApi = () => {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [data, setData] = useState(null);

    const execute = useCallback(async (method, url, config = {}) => {
        setLoading(true);
        setError(null);
        setData(null);

        try {
            const response = await api({
                method,
                url,
                ...config,
            });
            
            setData(response.data);
            return response.data;
        } catch (err) {
            setError(err);
            throw err;
        } finally {
            setLoading(false);
        }
    }, []);

    const get = useCallback((url, config) => execute('get', url, config), [execute]);
    const post = useCallback((url, data, config) => execute('post', url, { data, ...config }), [execute]);
    const put = useCallback((url, data, config) => execute('put', url, { data, ...config }), [execute]);
    const del = useCallback((url, config) => execute('delete', url, config), [execute]);

    return {
        loading,
        error,
        data,
        get,
        post,
        put,
        del,
    };
};

export default useApi;