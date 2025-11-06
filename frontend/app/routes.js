import { layout, route, index } from "@react-router/dev/routes";

export default [
    layout('./layouts/auth.jsx', [
        route("login", "screens/auth/login.jsx"),
        route("register", "screens/auth/register.jsx"),
    ]),

    layout('./layouts/app.jsx', [
        index("screens/dashboard.jsx"),
        
        route("timers", "timers/screens/index.jsx"),
    ]),
];
