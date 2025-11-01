import logo from '@/../public/logo.png';

export default function Bootstrapping() {
    return <Layout />
}

export const Layout = ({ children }) => {
    return (
        <section className='h-screen w-full'>
            <main className={`absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 transition-all duration-1000`}>
            <div className="flex flex-col items-center">
                <img src={logo} alt={"logo"} height={200} width={200} />
                {children}
            </div>
            </main>
        </section>
    );
}
