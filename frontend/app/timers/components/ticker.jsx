import { useCallback, useEffect, useRef, useState } from 'react'
import { motion, useAnimation } from "motion/react";
import clockDial from "@/timers/assets/clock-dial.png";
import TickerModel from '@/timers/models/ticker.model';
import { Check, Pause, Play, RotateCcw } from 'lucide-react';

export default function Ticker () {
    const [ticker, setTicker] = useState(new TickerModel());
    const [running, setRunning] = useState(false);
    const tickerTimeoutRef = useRef(null);
    const animateControls = useAnimation();

    const tick = useCallback(() => {
        tickerTimeoutRef.current = setTimeout(() => {
            setTicker(ticker.tick().clone());
        }, 1000);
        return () => clearTimeout(tickerTimeoutRef.current);
    }, []);

    const handlePlay = () => {
        setRunning(true);
    }

    const handlePause = () => {
        setRunning(false);
    }

    useEffect(() => {
        if (running) return tick();
    }, [ticker.seconds]);

    useEffect(() => {
        if (running) {
            animateControls.start({ rotate: 360 });
            tick();
        } else {
            clearTimeout(tickerTimeoutRef.current);
            animateControls.stop();
        }
    }, [running]);

    return (
        <section className='flex flex-col justify-center items-center'>
            <main className='flex flex-col justify-center items-center'>
                <main className="w-64 h-64 relative flex items-center justify-center">
                    <motion.div 
                        initial={{ rotate: 0 }}
                        animate={animateControls}
                        transition={{
                            duration: 60,
                            ease: "linear",
                            repeat: Infinity,
                            repeatType: "loop"
                        }}
                        className="
                            absolute top-0 left-0 w-full h-full rounded-full 
                            flex justify-center 
                            bg-radial-[at_0%_0%] from-primary-400 to-primary-900 to-95% border-4 border-accent-700
                        "
                    >
                        <img src={clockDial} alt="clock dial" className='w-full h-full' />
                    </motion.div>
                    <div className="relative h-full flex flex-col items-center justify-center z-[1]">
                        <span className='absolute top-18 display-text text-base'>{ticker.days == 0 && ticker.formattedDays}</span>
                        <span className='display-text text-6xl'>{ticker.formattedTime}</span>
                    </div>
                </main>
            </main>
            <footer className='mt-20 w-full flex items-center justify-around'>
                <div onClick={ticker.stop} className="rounded-full p-2 border-2 bg-accent-200 dark:bg-accent-800 text-green-600 border-primary">
                    <Check size={31} strokeWidth={3} />
                </div>
                <div onClick={running ? handlePause : handlePlay} className="rounded-full bg-primary p-3 text-primary-foreground">
                    {
                        running
                            ? <Pause size={43} strokeWidth={2} />
                            : <Play size={43} strokeWidth={2} />
                    }
                </div>
                <div onClick={ticker.reset} className="rounded-full p-2 border-2 bg-accent-200 dark:bg-accent-800 text-accent-600 dark:text-accent-400 border-primary">
                    <RotateCcw size={33} strokeWidth={2} />
                </div>
            </footer>
        </section>
    );
}