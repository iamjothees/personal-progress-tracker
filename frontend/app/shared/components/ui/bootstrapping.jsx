import logo from '@/../public/logo.png';

export default function Bootstrapping() {
    return (
        <section className='h-screen w-full'>
            <main className={`absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center`}>
                <img src={logo} alt={"logo"} height={200} width={200} />
            </main>
        </section>
    )
}
