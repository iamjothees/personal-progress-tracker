import {
  Links,
  Meta,
  Scripts,
  ScrollRestoration,
} from "react-router";

import Main from "./app";

import "./app.css";

export const links = () => [
  { rel: "preconnect", href: "https://fonts.googleapis.com" },
  {
    rel: "preconnect",
    href: "https://fonts.gstatic.com",
    crossOrigin: "anonymous",
  },
  {
    rel: "stylesheet",
    href: "https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap",
  },
];

export function Layout({ children }) {
  return (
    <html lang="en" className="dark">
      <head>
        <meta charSet="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <style>
          @import url('https://fonts.googleapis.com/css2?family=Momo+Trust+Display&family=Momo+Trust+Sans:wght@200..800&display=swap');
        </style>
        <Meta />
        <Links />
      </head>
      <body className="w-full min-h-screen flex flex-col">
        {children}
        <ScrollRestoration />
        <Scripts />
      </body>
    </html>
  );
}

export default function App() {
  return <Main />;
}

export function ErrorBoundary({ error }) {
  return <Main.ErrorBoundary error={error} />;
}
