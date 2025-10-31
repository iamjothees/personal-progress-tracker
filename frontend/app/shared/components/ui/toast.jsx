import { cn } from '@/shared/lib/utils';
import { X, CheckCircle2, XCircle, Info } from 'lucide-react';
import { useEffect, useState } from 'react';

const toastTypes = {
    success: {
        icon: CheckCircle2,
        iconClass: 'text-green-600',
        bgClass: 'bg-green-50',
    },
    error: {
        icon: XCircle,
        iconClass: 'text-red-600',
        bgClass: 'bg-red-50',
    },
    info: {
        icon: Info,
        iconClass: 'text-blue-600',
        bgClass: 'bg-blue-50',
    },
};

const Toast = ({ message, type, onClose }) => {
    const { icon: Icon, iconClass, bgClass } = toastTypes[type] || toastTypes.info;
    const [isVisible, setIsVisible] = useState(true);

    useEffect(() => {
        const timer = setTimeout(() => {
            setIsVisible(false);
        }, 2500); // Start fade out before actual removal

        return () => clearTimeout(timer);
    }, []);

    const handleClose = () => {
        setIsVisible(false);
        // Allow time for animation before calling onClose
        setTimeout(onClose, 500); 
    };

    return (
        <div className={cn(
            'p-4 rounded-lg shadow-lg flex items-center justify-between w-full',
            bgClass,
            isVisible ? 'animate-fade-in-up' : 'animate-fade-out-up'
        )}>
            <div className="flex items-center">
                <Icon className={cn('h-6 w-6 mr-3', iconClass)} />
                <span className="text-sm font-medium text-gray-800">{message}</span>
            </div>
            <button onClick={handleClose} className="ml-4 text-gray-500 hover:text-gray-800">
                <X className="h-5 w-5" />
            </button>
        </div>
    );
};

export default Toast;
