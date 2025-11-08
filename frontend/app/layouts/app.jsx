import React from 'react'
import { useLocation, useOutlet } from 'react-router';
import Header from '@/layouts/components/header';
import Footer from '@/layouts/components/footer';
import { AnimatePresence, motion } from 'motion/react';

const slideVariants = {
    initial: {
        x: '100%', // Start from right
        opacity: 0,
    },
    animate: {
        x: '0%', // Slide to center
        opacity: 1,
        transition: {
            duration: 0.5,
            ease: 'easeInOut',
        },
    },
    exit: {
        x: '-100%', // Slide out to left
        opacity: 0,
        transition: {
            duration: 0.5,
            ease: 'easeInOut',
        },
    },
};

function AppLayout() {
    const location = useLocation();
    const element = useOutlet();
    return (
        <section className='flex-1 flex flex-col'>
            <Header />
            <main className='flex-1 flex flex-col'>
                <AnimatePresence mode="wait">
                    {
                        element &&
                        <motion.div
                            key={location.pathname}
                            variants={slideVariants}
                            initial="initial"
                            animate="animate"
                            exit="exit"
                            transition={{ duration: 0.5 }}
                        >
                            {element}
                        </motion.div>
                    }
                </AnimatePresence>
            </main>
            <Footer />
        </section>
    )
}

export default AppLayout