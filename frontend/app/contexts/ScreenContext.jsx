import { createContext, useContext, useState } from 'react';

const ScreenContext = createContext();

const defaultScreen = {
    screenTitle: import.meta.env.VITE_APP_NAME,
    showHeader: true,
    showFooter: true,
    loading: false,
}

export function ScreenContextProvider({children}) {
    const [screen, setScreen] = useState({...defaultScreen, showHeader: false});

    return (
        <ScreenContext value={{ screen, setScreen, defaultScreen }} >
            {children}
        </ScreenContext>
    );
}

export const useScreen = () => useContext(ScreenContext);
