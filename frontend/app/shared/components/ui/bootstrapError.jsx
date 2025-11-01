import { useEffect, useState } from "react";
import { Layout } from "./bootstrapping";
import { cn } from "@/shared/lib/utils";

export default function BootstrapError() {
    const [show, setShow] = useState(false);

    useEffect(() => setShow(true),[]);
    return (
        <Layout>
            <div className={cn("text-center transition-all duration-1000 ease-in", show ? "opacity-100" : "opacity-0")}>
                <p className="display-text text-red-500 mb-2"> Something went wrong! Please restart the app </p>
                <p className="display-text text-xs text-red-300"> If the problem persists, please contact support </p>
            </div>
        </Layout>
        
    )
}
