import { isRouteErrorResponse, Outlet } from "react-router";
import ApiPreflight from "@/core/http/apiPreflight";
import { ScreenContextProvider } from "./contexts/ScreenContext";
import { ToastProvider } from "./contexts/ToastContext";
import AuthProvider from "./core/auth/auth.provider";

const App = function() {
  return (
    <ApiPreflight>
      <ScreenContextProvider>
        <ToastProvider>
          <AuthProvider>
            <main className="">
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
