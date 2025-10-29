import useFetcher from "@/shared/hooks/useFetcher";

export default function Dashboard() {
    const fetcher = useFetcher({url: '/timers' });

    return (
        <div>
            {
                fetcher.loading && "Loading..."
            }
            {
                fetcher.error && "Error"
            }
            {
                fetcher.data && (fetcher.data.length > 0 ? fetcher.data.map(timer => <li key={timer.id}>{timer.name}</li>) : "No timers started yet")
            }
        </div>
);
}
