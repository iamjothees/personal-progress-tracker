import { useState } from "react";
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { Button } from "@/shared/components/ui/button";
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/shared/components/ui/form";
import { Input } from "@/shared/components/ui/input";
import { useNavigate } from "react-router";
import { useToast } from "@/contexts/ToastContext";
import { useAuth } from "@/core/auth/auth.context";

const formSchema = z.object({
  email: z.string().min(1, { message: "Email is required" }).email("Please enter a valid email address"),
  password: z.string().min(1, { message: "Password is required" }).min(8, { message: "Password must be at least 8 characters" }),
});

export default function Login() {
  const [loading, setLoading] = useState(false);
  const [generalError, setGeneralError] = useState("");
  const { login } = useAuth();
  
  const form = useForm({
    resolver: zodResolver(formSchema),
    defaultValues: {
      email: "",
      password: "",
    },
  });

  const navigate = useNavigate();
  const { showToast } = useToast();

  function onSubmit(values) {
    setLoading(true);
    setGeneralError("");
    
    login(values)
      .then((user) => {
        showToast(`Welcome back! ${user.name}`, "success");
        navigate("/dashboard");
      })
      .catch((error) => {
        // Handle login error - display specific error from auth service
        setGeneralError(error.message || "An error occurred during login");
      })
      .finally(() => {
        setLoading(false);
      });
  }

  return (
    <div className="container mx-auto flex items-center justify-center min-h-[80vh]">
      <div className="w-full max-w-md space-y-8">
        <div className="text-center">
          <h2 className="text-3xl font-bold">Login</h2>
          <p className="mt-2 text-muted-foreground">Enter your credentials to continue</p>
        </div>
        
        <Form {...form}>
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
            {generalError && (
              <div className="text-red-500 text-sm text-center">
                {generalError}
              </div>
            )}
            <FormField
              control={form.control}
              name="email"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Email</FormLabel>
                  <FormControl>
                    <Input placeholder="your@email.com" {...field} disabled={loading} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />
            
            <FormField
              control={form.control}
              name="password"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Password</FormLabel>
                  <FormControl>
                    <Input type="password" placeholder="••••••••" {...field} disabled={loading} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />
            
            <Button type="submit" className="w-full" disabled={loading}>
              {loading ? "Signing in..." : "Sign in"}
            </Button>
          </form>
        </Form>
        
        <div className="text-center text-sm text-muted-foreground mt-4">
          <p>Don't have an account? <a href="/register" className="text-primary hover:underline">Sign up</a></p>
        </div>
      </div>
    </div>
  );
}
