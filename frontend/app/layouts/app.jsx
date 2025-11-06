import { useToast } from '@/contexts/ToastContext';
import { useAuth } from '@/core/auth/auth.provider';
import { Button } from '@/shared/components/ui/button'
import { House, LogOut, Timer, User } from 'lucide-react'
import React from 'react'
import { Link, Outlet, useNavigate } from 'react-router';
import Footer from '@/layouts/components/footer';

function AppLayout() {
    return (
        <section className='flex-1 flex flex-col'>
            <Header />
            <main className='flex-1'>
                <Outlet />
            </main>
            <Footer />
        </section>
    )
}

export default AppLayout

const Header = () => {
    const { logout } = useAuth();
    const { showToast } = useToast();
    const navigate = useNavigate();

    const handleLogout = () => {
        logout()
            .then(() => {
                showToast("Bye! You are now logged out", "success");
                navigate("/login");
            })
            .catch((error) => {
                console.error(error);
                showToast("Error logging out. Please try again", "error");
            });
    }
    return (
        <header className='flex justify-end'>
            <Button onClick={handleLogout} size="icon"><LogOut /></Button>
        </header>
    )
}