import React, { useState, useEffect } from 'react'
import { Outlet } from 'react-router'
import logo from '@/../public/logo.png';

export default function AuthLayout() {
    const [animate, setAnimate] = useState(false);

    useEffect(() => {
        const animateTimer = setTimeout(() => setAnimate(true), 2000);
        return () => clearTimeout(animateTimer);
    }, []);

    return (
        <section className='h-screen w-screen'>
            <header className={`absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 transition-all duration-1000 ease-in-out ${animate ? 'auth-header-animate' : ''}`}>
                <img src={logo} alt={"logo"} height={200} width={200} />
            </header>
            <main className={`w-[80%] w-max-[400px] absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 transition-all duration-1000 ease-in-out ${animate ? '' : 'opacity-0'}`}>
                <Outlet />
            </main>
        </section>
    )
}
