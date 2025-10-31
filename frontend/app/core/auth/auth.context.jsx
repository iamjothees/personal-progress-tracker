import { createContext, useContext, useEffect, useState } from 'react';
import authService from '@/core/auth/auth.service';
import { useLocation, useNavigate } from 'react-router';

const AuthContext = createContext();

export default function AuthProvider({children}) {
    const [user, setUser] = useState(undefined);
    const navigate = useNavigate();
    const location = useLocation();
    const [isAuthRoute, setIsAuthRoute] = useState(false);
    

    const login = (loginUserData) => {
        return new Promise((resolve, reject) => {
            authService.login(loginUserData)
                .then((user) => {
                    setUser(user);
                    return user;
                })
                .then(resolve)
                .catch(reject);
        });
    };

    const logout = () => {
        return new Promise((resolve, reject) => {
            authService.logout()
                .then(() => {
                    setUser(undefined);
                    return resolve(true);
                })
                .catch(reject);
        });
    };

    const signup = (signupUserData) => {
        return new Promise((resolve, reject) => {
            authService.signup(signupUserData)
                .then((user) => {
                    setUser(user);
                    return user;
                })
                .then(resolve)
                .catch(reject);
        });
    };

    useEffect(() => {
        authService.getCurrentUser()
            .then((user) => setUser(user));
    }, []);

    useEffect(() => {
        if (isAuthRoute && user) {
            navigate("/dashboard");
        }else if(isAuthRoute === false && user === null){
            navigate("/login");
        }
    }, [user]);

    useEffect(() => {
        setIsAuthRoute(['/login', '/register'].includes(location.pathname.replace(/\/+$/, '')));
    }, [location.pathname]);

    return (
        <AuthContext.Provider value={{ user, setUser, login, logout, signup }}>
            {
                (user === undefined)
                    ? "Authenticating..."
                    : (((isAuthRoute && user) || (isAuthRoute === false && user === null))
                        ? "Redirecting..."
                        : children)
            }
            
        </AuthContext.Provider>
    );
}

export const useAuth = () => useContext(AuthContext);