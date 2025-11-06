import { useToast } from '@/contexts/ToastContext';
import { useAuth } from '@/core/auth/auth.provider';
import { Button } from '@/shared/components/ui/button'
import { LogOut } from 'lucide-react'
import React from 'react'
import { Outlet, useNavigate } from 'react-router';

function AppLayout() {
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
        <section>
            <header className='flex justify-end'>
                <Button onClick={handleLogout} size="icon"><LogOut /></Button>
            </header>
            <main>
                <Outlet />
            </main>
        </section>
    )
}

export default AppLayout