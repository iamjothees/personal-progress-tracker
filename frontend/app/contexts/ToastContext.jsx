import { createContext, useContext, useState, useCallback } from 'react';
import Toast from '@/shared/components/ui/toast';

const ToastContext = createContext();

export function ToastProvider({ children }) {
    const [toasts, setToasts] = useState([]);

    const showToast = useCallback((message, type = 'info') => {
        const id = `${Date.now()}-${Math.random()}`;
        setToasts((prevToasts) => [{ id, message, type, isVisible: true }, ...prevToasts]);
        setTimeout(() => {
            setToasts((prevToasts) =>
                prevToasts.map((toast) =>
                    toast.id === id ? { ...toast, isVisible: false } : toast
                )
            );
            setTimeout(() => {
                setToasts((prevToasts) => prevToasts.filter((toast) => toast.id !== id));
            }, 500); // Allow time for fade-out animation
        }, 2500); // Toast visible duration
    }, []);

    const closeToast = (id) => {
        setToasts((prevToasts) =>
            prevToasts.map((toast) =>
                toast.id === id ? { ...toast, isVisible: false } : toast
            )
        );
        setTimeout(() => {
            setToasts((prevToasts) => prevToasts.filter((toast) => toast.id !== id));
        }, 500); // Allow time for fade-out animation
    };

    return (
        <ToastContext.Provider value={{ showToast }}>
            {children}
            <div className="fixed bottom-4 inset-x-4 z-[9999] space-y-2 flex flex-col-reverse items-center max-h-[40vh] overflow-hidden pointer-events-none">
                {toasts.map((toast) => (
                    <div key={toast.id} className="w-full pointer-events-auto">
                        <Toast message={toast.message} type={toast.type} onClose={() => closeToast(toast.id)} />
                    </div>
                ))}
            </div>
        </ToastContext.Provider>
    );
}

export const useToast = () => useContext(ToastContext);
