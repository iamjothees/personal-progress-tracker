import React, { useCallback, useEffect, useState } from 'react'
import { motion } from "motion/react";
import clockDial from "@/timers/assets/clock-dial.png";
import Ticker from '@/timers/models/ticker.model';

function timers() {
    const [ticker, setTicker] = useState(new Ticker());

    useEffect(() => {
        return tick();
    }, [ticker.seconds]);

    const tick = useCallback(() => {
        let timeout = setTimeout(() => {
            setTicker(ticker.tick().clone());
            
        }, 1000);
        return () => clearTimeout(timeout);
    }, []);

    return (
        <section className="flex-1 flex flex-col items-center justify-center">
            <main className='w-64 h-64 relative flex items-center justify-center'>
                <motion.div 
                    initial={{ rotate: 0 }}
                    animate={{ rotate: 360 }}
                    transition={{
                        duration: 60,
                        ease: "linear",
                        repeat: Infinity,
                        repeatType: "loop"
                    }}
                    className="
                        z-[-1] absolute top-0 left-0 w-full h-full rounded-full 
                        flex justify-center 
                        bg-radial-[at_0%_0%] from-primary-400 to-primary-900 to-95% border-4 border-accent-700
                    "
                >
                    <img src={clockDial} alt="clock dial" className='w-full h-full' />
                </motion.div>
                <div className="relative h-full flex flex-col items-center justify-center">
                    <span className='absolute top-18 display-text text-base'>{ticker.days == 0 && ticker.formattedDays}</span>
                    <span className='display-text text-6xl'>{ticker.formattedTime}</span>
                    <span className='absolute bottom-12 display-text text-5xl'>{ticker.formattedSeconds}</span>
                </div>
            </main>
        </section>
    )
}

export default timers