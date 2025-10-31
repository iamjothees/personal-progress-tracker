import { layout, route } from "@react-router/dev/routes";

export default [
    route("dashboard", "screens/dashboard.jsx"),

    layout('./layouts/auth.jsx', [
        // route("signup", "screens/signup.jsx"),
        route("login", "screens/login.jsx"),
    ]),
];
