import logo from '@/shared/app-specific/logo';
import { Outlet } from 'react-router';
import { motion } from "motion/react";
import { useEffect, useState } from 'react';

export default function AuthLayout() {
    const [showMain, setShowMain] = useState(false);

    useEffect(() => {
        setTimeout(() => setShowMain(true), 1000);
    }, []);

    return (
        <section className='min-h-screen flex flex-col justify-center items-center'>
            <motion.header 
                layout 
                transition={{ layout: { duration: 1.5 }, }}
                className="z-[-1]"
            >
                <img src={logo} alt={"logo"} height={200} width={200} />
            </motion.header>
            {
                showMain && 
                <motion.main
                    initial={{ opacity: 0, scale: 0.5 }}
                    animate={{ 
                        opacity: 1,
                        scale: 1,
                        transition:{ duration: 1.5 }
                    }}
                    className={`w-[80%] max-w-[350px]`}
                >
                    <Outlet />
                </motion.main>
            }
        </section>
    )
}