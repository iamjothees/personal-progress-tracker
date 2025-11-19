import { isRouteErrorResponse, Outlet } from "react-router";
import ApiPreflight from "@/core/http/apiPreflight";
import { ScreenContextProvider } from "./contexts/ScreenContext";
import { ToastProvider } from "./contexts/ToastContext";
import AuthProvider from "./core/auth/auth.provider";
import dayjs from "dayjs";
import 'dayjs/locale/en-in';
import relativeTime from 'dayjs/plugin/relativeTime';
import duration from 'dayjs/plugin/duration';
import advancedFormat from 'dayjs/plugin/advancedFormat'; 

// Configure dayjs
dayjs
  .extend(relativeTime)
  .extend(duration)
  .extend(advancedFormat)
  .locale("en-in");

const App = function() {
  return (
    <ApiPreflight>
      <ScreenContextProvider>
        <ToastProvider>
          <AuthProvider>
            <main className="flex-1 flex flex-col">
              <Outlet />
            </main>
          </AuthProvider>
        </ToastProvider>
      </ScreenContextProvider>
    </ApiPreflight>
  );
}

App.ErrorBoundary = function({ error }) {
  let message = "Oops!";
  let details = "An unexpected error occurred.";
  let stack;

  if (isRouteErrorResponse(error)) {
    message = error.status === 404 ? "404" : "Error";
    details =
      error.status === 404
        ? "The requested page could not be found."
        : error.statusText || details;
  } else if (import.meta.env.DEV && error && error instanceof Error) {
    details = error.message;
    stack = error.stack;
  }

  return (
    <main className="pt-16 p-4 container mx-auto">
      <h1>{message}</h1>
      <p>{details}</p>
      {stack && (
        <pre className="w-full p-4 overflow-x-auto">
          <code>{stack}</code>
        </pre>
      )}
    </main>
  );
}

export default App;
