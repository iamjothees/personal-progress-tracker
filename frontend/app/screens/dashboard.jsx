import { Button } from "@/components/ui/button";
import { Outlet } from "react-router";

export default function Dashboard() {
    return (
        <div>
        <h1 className="underline">Dashboard</h1>
        <Button>Logout</Button>
        <Outlet />
        </div>
);
}
