import { House, LayoutDashboard, Timer, User } from 'lucide-react'
import { Link } from "react-router";
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
            <MenuItem to="/" icon={<Timer size={25} />} text="Timers" isHighlighted={true}/>
            <MenuItem to="/" icon={<LayoutDashboard size={25} />} text="Tasks"/>
            <MenuItem to="/" icon={<User size={25} />} text="Profile"/>
        </div>
    )
}

const MenuItem = function({isHighlighted = false, icon = <div />, text = "", to = "/"}){
    return (
        <Link
            to={to}
            className={cn(
                `flex flex-col items-center gap-1`,
                isHighlighted 
                    ? 'text-primary-500 dark:text-primary-500'
                    : 'text-accent-light-500 dark:text-accent-500'
            )}
        >
            {icon}
            <span className="text-xs">{text}</span>
        </Link>
    )
}