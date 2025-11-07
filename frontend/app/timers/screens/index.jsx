import useFetcher from "@/shared/hooks/useFetcher";
import Ticker from "@/timers/components/ticker"

function timers() {
    const fetcher = useFetcher({url: '/timers' });

    return (
        <section className="flex-1 flex flex-col items-center justify-center">
            <main className='flex-1 flex flex-col items-center justify-center'>
                { fetcher.loading && <p>Loading...</p> }
                { fetcher.error && <p>Error</p> }
                { 
                    fetcher.data && 
                    fetcher.data.timers?.length > 0 
                        ? "Redirecting to first timer..."
                        : <Ticker />

                }
            </main>
        </section>
    )
}

export default timers