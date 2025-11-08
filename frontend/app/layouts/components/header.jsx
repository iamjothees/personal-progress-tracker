import { useToast } from '@/contexts/ToastContext';
import { useAuth } from '@/core/auth/auth.provider';
import { Button } from '@/shared/components/ui/button'
import { LogOut } from 'lucide-react'
import React from 'react'
import { Link, useNavigate } from 'react-router';

const Header = () => {
    const { user, logout } = useAuth();
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
        <header className='flex justify-between items-center p-4'>
            <div className=" flex items-center justify-center rounded-full bg-gradient-to-br from-primary-400 to-primary-900 to-95%  h-12 w-12">
                <Link to="/profile" className="display-text text-xl tracking-widest">
                    {user.initial}
                </Link>
            </div>
            <div className="flex items-center gap-4">
                <Button onClick={handleLogout}>
                    <LogOut size={20} />
                </Button>
            </div>
        </header>
    )
}

export default Header;