import { Button } from "@/components/ui/button";
import useFetcher from "@/hooks/useFetcher";
import api from "@/services/api";
import { useEffect } from "react";
import { Outlet } from "react-router";

export default function Login() {
    const { data, loading, error } = useFetcher({url: '/login', method: 'post', data: { email: 'iamjothees@gmail.com', password: 'joe@dev' }});

    useEffect(() => {
        console.log({data, loading, error});
    }, [data, loading, error]);
    return (
        <div>
            {
                loading && "Loading..."
            }
            {
                error && "Error"
            }
            {
                data && "Hey Joe"
            }
        </div>
);
}
