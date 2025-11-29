import { useEffect, useRef, useState } from 'react'
import { AnimatePresence, motion, useAnimation } from "motion/react";
import clockDial from "@/timers/assets/clock-dial.png";
import TickerModel from '@/timers/models/ticker.model';
import { Check, LoaderCircle, Pause, Play, RotateCcw } from 'lucide-react';
import { pause, reset, resume, start, stop } from '@/timers/services/timer.service';
import { useNavigate } from 'react-router';
import { useToast } from '@/contexts/ToastContext';
import { cn } from '@/shared/lib/utils';
import TickerEngine from '@/timers/services/ticker.service';

export default function Ticker() {
    // for functionalities
    const navigate = useNavigate();
    const [ticker, setTicker] = useState(new TickerModel());

    // Use useRef to persist TickerEngine instance across renders
    const tickerEngineRef = useRef(null);
    if (!tickerEngineRef.current) {
        tickerEngineRef.current = new TickerEngine(ticker.clone(), {
            onTick: (tickerModel) => setTicker(tickerModel.clone())
        });
    }
    const tickerEngine = tickerEngineRef.current;

    const [timer, setTimer] = useState(null);

    // Update the engine's model reference when ticker changes
    useEffect(() => {
        tickerEngine.updateModel(ticker);
    }, [ticker, tickerEngine]);

    // for ui changes
    const [showBackdrop, setShowBackdrop] = useState(false);
    const [loading, setLoading] = useState(false);
    const animateControls = useAnimation();
    const { showToast } = useToast();

    const syncTimer = async ({ method }) => {
        setLoading(true);
        return method()
            .then((res) => {
                setLoading(false);
                return res;
            })
            .catch((err) => {
                showToast("A server error occurred. Please try again.", "error");
                if (import.meta.env.DEV) {
                    console.error(err);
                } else {
                    navigate(0);
                }
            });
    }

    const handlePlay = () => {
        const secondsElapsed = tickerEngine.getPauseDuration();
        syncTimer({ method: () => (timer ? resume({ timer, secondsElapsed }) : start()) })
            .then((timer) => {
                setTimer(timer);
            });

        tickerEngine.start();
    }

    const handlePause = () => {
        const secondsElapsed = tickerEngine.getSecondsSinceLastAction();
        syncTimer({ method: () => pause({ timer, secondsElapsed }) })
            .then((timer) => {
                setTimer(timer);
            });

        tickerEngine.pause();
    }

    const handleReset = () => {
        syncTimer({ method: () => reset({ timer }) })
            .then(() => {
                setTimer(null);
            });

        tickerEngine.reset();
    }

    const handleComplete = () => {
        syncTimer({ method: () => stop({ timer, secondsElapsed: tickerEngine.getTotalAccumulatedSeconds() }) })
            .then((timer) => {
                setTimer(timer);
                setTicker(ticker.complete().clone());
            });

        tickerEngine.pause();
    }

    useEffect(() => {
        if (ticker.running) {
            animateControls.start({ rotate: 360 });
        } else {
            animateControls.stop();
        }

    }, [ticker.running]);

    useEffect(() => {
        if (loading || (!ticker.running && !ticker.started)) {
            setShowBackdrop(true);
        } else {
            setShowBackdrop(false);
        }
    }, [ticker.started, ticker.running, loading]);

    return (
        <section className='flex flex-col justify-center items-center'>
            <motion.main
                layout
                transition={{ layout: { duration: 0.8 }, }}
                className="
                    w-64 h-64 relative flex items-center justify-center rounded-full 
                    bg-radial-[at_0%_0%] from-primary-400 to-primary-900 to-150% dark:95%
                    z-0
                "
            >
                {/* Clock dial */}
                <motion.div
                    initial={{ rotate: 0 }}
                    animate={animateControls}
                    transition={{
                        duration: 60,
                        ease: "linear",
                        repeat: Infinity,
                        repeatType: "loop"
                    }}
                    className="absolute z-0 top-0 left-0 w-full h-full"
                >
                    <img src={clockDial} alt="clock dial" className='w-full h-full' />
                </motion.div>

                {/* Backdrop */}
                <AnimatePresence mode="wait">
                    {
                        showBackdrop &&
                        <motion.div
                            key="backdrop" initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }}
                            className="
                                absolute top-0 left-0 z-1
                                rounded-full w-full h-full
                                bg-accent-400/30 dark:bg-accent-900/60
                            "
                        />
                    }
                </AnimatePresence>

                {/* Display */}
                <div className="relative w-full h-full z-2 text-primary-foreground">
                    <AnimatePresence mode="wait">
                        {
                            loading
                                ? <motion.div
                                    key="loading" initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }}
                                    className='h-full w-full flex flex-col items-center justify-center text-primary'
                                >
                                    <LoaderCircle size={50} className='animate-spin' />
                                </motion.div>
                                : (ticker.running || ticker.started)
                                    ? <motion.div
                                        key="display" initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }}
                                        className="relative h-full w-full flex flex-col items-center justify-center"
                                    >
                                        {ticker.days > 0 && <span className='absolute top-18 display-text text-base'>{ticker.formattedDays} Days</span>}
                                        <span className='display-text text-6xl'>{ticker.formattedTime}</span>
                                        <span className='absolute bottom-14 display-text text-3xl'>{ticker.formattedSeconds}</span>
                                    </motion.div>
                                    : <motion.div
                                        onClick={handlePlay}
                                        key="start" initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }}
                                        className="w-full h-full flex-1 flex items-center justify-center cursor-pointer"
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
                (ticker.running || ticker.started) &&
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
                                <motion.div onClick={loading ? null : handleComplete}
                                    whileHover={{ scale: 1.1 }}
                                    whileTap={{ scale: 0.9 }}
                                    className={cn("rounded-full p-2 border-2 bg-green-100/50 dark:bg-green-900/50 text-green-600 border-primary", loading && "cursor-not-allowed animate-pulse")}
                                >
                                    <Check size={31} strokeWidth={3} />
                                </motion.div>
                                <motion.div
                                    onClick={loading ? null : (ticker.running ? handlePause : handlePlay)}
                                    className={cn("rounded-full bg-primary p-3 text-primary-foreground", loading && "cursor-not-allowed animate-pulse")}
                                    whileHover={{ scale: 1.1 }}
                                    whileTap={{ scale: 0.9 }}
                                >
                                    <AnimatePresence mode="wait">
                                        {
                                            ticker.running
                                                ? <motion.div key="pause" initial={{ opacity: 0 }} animate={{ opacity: 1, transition: { duration: 0.2 } }} exit={{ opacity: 0, transition: { duration: 0.2 } }}>
                                                    <Pause size={43} fill="currentColor" />
                                                </motion.div>
                                                : <motion.div key="play" initial={{ opacity: 0 }} animate={{ opacity: 1, transition: { duration: 0.2 } }} exit={{ opacity: 0, transition: { duration: 0.2 } }}>
                                                    <Play size={43} fill="currentColor" />
                                                </motion.div>
                                        }
                                    </AnimatePresence>
                                </motion.div>
                                <motion.div onClick={loading ? null : handleReset}
                                    whileHover={{ scale: 1.1 }}
                                    whileTap={{ scale: 0.9 }}
                                    className={cn("rounded-full p-2 border-2 bg-accent-200 dark:bg-accent-800 text-accent-600 dark:text-accent-400 border-primary", loading && "cursor-not-allowed animate-pulse")}
                                >
                                    <RotateCcw size={33} strokeWidth={2} />
                                </motion.div>
                            </>
                    }
                </motion.footer>
            }
        </section>
    );
}