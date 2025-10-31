import { useState } from 'react';

function ImageWithFallback({ src, alt="", fallbackSrc="#", ...props }) {
  const [error, setError] = useState(false);

  return (
    <img
      src={error ? fallbackSrc : src}
      alt={alt}
      onError={() => setError(true)}
      {...props}
    />
  );
}

export default ImageWithFallback