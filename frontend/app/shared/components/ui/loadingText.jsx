import { useEffect, useState } from 'react';

export default function LoadingText({text}) {
    const [content, setContent] = useState('');
    const [currentIndex, setCurrentIndex] = useState(0);
    const contentArray = [0,1,2,3].map((i) => `${text}${('.').repeat(i)}`);


    useEffect(() => {
        const textContentInterval = setInterval(() => {
            setCurrentIndex((prevIndex) => (prevIndex + 1) % contentArray.length);
        }, 600);
        return () => clearInterval(textContentInterval);
    }, []);

    useEffect(() => {
        setContent(contentArray[currentIndex]);
    }, [currentIndex]);

    return content;
}
