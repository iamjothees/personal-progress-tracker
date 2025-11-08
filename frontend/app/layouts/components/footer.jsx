import { House, LayoutDashboard, Timer, User } from 'lucide-react'
import { Link, useLocation } from "react-router";
import { cn } from "@/shared/lib/utils";
import { motion } from 'motion/react';


function Footer() {
    return (
        <motion.footer
            initial={{ y: -100 }}
            animate={{ y: 0 }}
            className="
                min-h-18 flex justify-around items-center
                border-t rounded-t-2xl
                bg-accent-light-300 dark:bg-accent-800
                animate-fade-in-up
            "
        >
            <Menu />
        </motion.footer>

    )
}

export default Footer

const Menu = function (){
    return (
        <div className='w-full h-full flex items-center justify-around'>
            <MenuItem to="/" icon={<House size={25} />} text="Home" />
            <MenuItem to="/timers" icon={<Timer size={25} />} text="Timers"/>
            <MenuItem to="/tasks" icon={<LayoutDashboard size={25} />} text="Tasks"/>
            <MenuItem to="/profile" icon={<User size={25} />} text="Profile"/>
        </div>
    )
}

const MenuItem = function({icon = <div />, text = "", to = "/"}){
    const location = useLocation();

    return (
        <Link
            to={to}
            className={cn(
                "flex flex-col items-center gap-1 transition-colors duration-300",
                location.pathname === to
                    ? 'text-primary-700 dark:text-primary-500'
                    : 'text-accent-light-500 dark:text-accent-500'
            )}
        >
            {icon}
            <span className="text-xs">{text}</span>
        </Link>
    )
}