import useFetcher from "@/shared/hooks/useFetcher";

export default function Login() {
    // const { data, loading, error } = useFetcher({url: '/login', method: 'post', data: { email: 'iamjothees@gmail.com', password: 'joe@dev' }});
    const { data, loading, error } = {data: true, loading: false, error: false};

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
