import { useEffect, useRef, useState } from 'react'
import { AnimatePresence, motion, useAnimation } from "motion/react";
import clockDial from "@/timers/assets/clock-dial.png";
import TickerModel from '@/timers/models/ticker.model';
import { Check, Pause, Play, RotateCcw } from 'lucide-react';

export default function Ticker () {
    const [ticker, setTicker] = useState(new TickerModel());
    const [running, setRunning] = useState(false);
    const tickerTimeoutRef = useRef(null);
    const animateControls = useAnimation();

    const tick = () => {
        tickerTimeoutRef.current = setTimeout(() => {
            setTicker(ticker.tick().clone());
        }, 1000);
        return () => clearTimeout(tickerTimeoutRef.current);
    };

    const handlePlay = () => {
        setRunning(true);
    }

    const handlePause = () => {
        setRunning(false);
    }

    const handleReset = () => {
        setRunning(false);
        setTicker(new TickerModel());
    }

    const handleComplete = () => {
        // Stop the ticker
        setRunning(false);
        setTicker(ticker.complete().clone());
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
            <motion.main 
                layout
                transition={{ layout: { duration: 0.8 }, }}
                className="
                    w-64 h-64 relative flex items-center justify-center rounded-full 
                    bg-radial-[at_0%_0%] from-primary-400 to-primary-900 to-150% dark:95%
                ">
                <motion.div 
                    initial={{ rotate: 0 }}
                    animate={animateControls}
                    transition={{
                        duration: 60,
                        ease: "linear",
                        repeat: Infinity,
                        repeatType: "loop"
                    }}
                    className="absolute top-0 left-0 w-full h-full"
                >
                    <img src={clockDial} alt="clock dial" className='w-full h-full' />
                </motion.div>
                <div className="w-full h-full z-[1] text-primary-foreground">
                    <AnimatePresence mode="wait">
                    {
                        (running || ticker.started)
                            ? <motion.div 
                                key="display" initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} 
                                className="relative h-full w-full flex flex-col items-center justify-center"
                            >
                                { ticker.days > 0 && <span className='absolute top-18 display-text text-base'>{ticker.formattedDays} Days</span> }
                                <span className='display-text text-6xl'>{ticker.formattedTime}</span>
                                <span className='absolute bottom-14 display-text text-3xl'>{ticker.formattedSeconds}</span>
                            </motion.div>
                            : <motion.div 
                                onClick={handlePlay} 
                                key="start" initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} 
                                className="
                                    rounded-full w-full h-full flex-1 flex items-center justify-center 
                                    bg-accent-400/30 dark:bg-accent-900/60 cursor-pointer
                                "
                            >
                                <Play size={120} fill="currentColor" 
                                    className="stroke-1 dark:stroke-primary-500 ml-2" 
                                />
                            </motion.div>
                    }
                    </AnimatePresence>
                </div>
            </motion.main>
            {
                (running || ticker.started) &&
                <motion.footer
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    className='mt-16 w-full flex items-center justify-around'
                >
                    {
                        ticker.completed === true 
                        ? <>
                            <motion.div 
                                initial={{ opacity: 0, scale: 0.5 }} animate={{ opacity: 1, scale: 1, transition: { delay: 0.7, duration: 0.7 } }} exit={{ opacity: 0, scale: 0.5, transition: { duration: 0.7 } }}
                                className="flex-grow text-center rounded-full py-1.5 border-2 bg-green-100/50 dark:bg-accent-700/50 text-green-600 border-green-700 dark:border-green-900"
                            >
                                Completed
                            </motion.div>
                        </>
                        : <>
                            <div onClick={handleComplete} className="rounded-full p-2 border-2 bg-green-100/50 dark:bg-green-900/50 text-green-600 border-primary">
                                <Check size={31} strokeWidth={3} />
                            </div>
                            <div onClick={running ? handlePause : handlePlay} className="rounded-full bg-primary p-3 text-primary-foreground">
                                <AnimatePresence mode="wait">
                                    {
                                        running
                                            ? <motion.div key="pause" initial={{ opacity: 0 }} animate={{ opacity: 1, transition: { duration: 0.2 }  }} exit={{ opacity: 0, transition: { duration: 0.2 } }}>
                                                <Pause size={43} fill="currentColor" />
                                            </motion.div>
                                            : <motion.div key="play" initial={{ opacity: 0 }} animate={{ opacity: 1, transition: { duration: 0.2 }  }} exit={{ opacity: 0, transition: { duration: 0.2 } }}>
                                                <Play  size={43} fill="currentColor" />
                                            </motion.div>
                                    }
                                </AnimatePresence>
                            </div>
                            <div onClick={handleReset} className="rounded-full p-2 border-2 bg-accent-200 dark:bg-accent-800 text-accent-600 dark:text-accent-400 border-primary">
                                <RotateCcw size={33} strokeWidth={2} />
                            </div>
                        </>
                    }
                </motion.footer>
            }
        </section>
    );
}