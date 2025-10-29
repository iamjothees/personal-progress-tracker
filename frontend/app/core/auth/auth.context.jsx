import { createContext, useContext, useEffect, useState } from 'react';
import authService from '@/core/auth/auth.service';
import { redirect } from 'react-router';

const AuthContext = createContext();

export function AuthProvider({ children }) {
    const [user, setUser] = useState(undefined);

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
        if (user === null) {
            redirect("/login");
        };
    }, [user]);

    if (user === undefined) {
        return "Auth loading...";
    }

    return (
        <AuthContext.Provider value={{ user, setUser, login, logout, signup }}>
            { (user === undefined) ? "Auth loading..." : children }
        </AuthContext.Provider>
    );
}

export const useAuth = () => useContext(AuthContext);