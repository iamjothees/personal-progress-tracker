import { Button } from '@/shared/components/ui/button'
import React from 'react'
import { useAuth } from '../auth.provider';
import { useNavigate } from 'react-router';

function DevLogin() {
    const { login } = useAuth();
    const navigate = useNavigate();
    const users = [
        {email: 'test@example.com', password: 'password'},
    ];

    const handleLogin = (user) => {
        login(user)
            .then(() => {
                navigate("/");
            })
            .catch((error) => {
                console.error(error);
            });
    }


    return (
        <div className="flex flex-col">
            {
                users.map(user => (
                    <Button key={user.email} type="button" onClick={() => handleLogin(user)}>Login AS {user.email}</Button>
                ))
            }
        </div>
    )
}

export default DevLogin;