import BootstrapError from "@/shared/components/app-specific/bootstrapError";
import Bootstrapping from "@/shared/components/app-specific/bootstrapping";
import axios from "axios";
import { useEffect, useState } from "react";

export default function ApiPreflight({ children }) {
  const [preflightSuccess, setPreflightSuccess] = useState(null);

  useEffect(() => {
    if (document.cookie.includes('XSRF-TOKEN')) {
        setPreflightSuccess(true);
        return;
    }

    axios.create({
        baseURL: import.meta.env.VITE_PREFLIGHT_URL,
        timeout: 5000,
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        withCredentials: true,
        withXSRFToken: true
    })
    .get('')
      .then(() => {
        setPreflightSuccess(true);
      })
      .catch(() => {
        setPreflightSuccess(false);
      });
  }, []);
  return (
    <>
      { preflightSuccess === null && <Bootstrapping /> }
      { preflightSuccess === false && <BootstrapError /> }
      { preflightSuccess === true && children }
    </>
  );
}