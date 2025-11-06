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
import { Link, useNavigate } from "react-router";
import { useToast } from "@/contexts/ToastContext";
import { useAuth } from "@/core/auth/auth.provider";
import LoadingText from "@/shared/components/ui/loadingText";

const formSchema = z.object({
  name: z.string().min(1, { message: "Name is required" }),
  email: z.string().min(1, { message: "Email is required" }).email("Please enter a valid email address"),
  password: z.string().min(1, { message: "Password is required" }).min(8, { message: "Password must be at least 8 characters" }),
  password_confirmation: z.string().min(1, { message: "Password confirmation is required" }),
}).refine((data) => data.password === data.password_confirmation, {
  message: "Passwords don't match",
  path: ["password_confirmation"],
});

export default function Register() {
  const [loading, setLoading] = useState(false);
  const [generalError, setGeneralError] = useState("");
  const { signup } = useAuth();
  
  const form = useForm({
    resolver: zodResolver(formSchema),
    defaultValues: {
      name: "Test User",
      email: `${Date.now()}@example.com`,
      password: "test@123",
      password_confirmation: "test@123",
    },
  });

  const navigate = useNavigate();
  const { showToast } = useToast();

  function onSubmit(values) {
    setLoading(true);
    setGeneralError("");
    
    // Remove password_confirmation from the data sent to the server
    const { password_confirmation, ...userData } = values;
    
    signup(userData)
      .then(() => {
        showToast(`Your account has been created. Login to get started`, "success");
        navigate("/login");
      })
      .catch((error) => {
        console.error(error);
        if (error.cause){
          setGeneralError(error.message);
          return;
        }
        showToast("Error creating account. Please try again", "error");
      })
      .finally(() => {
        setLoading(false);
      });
  }

  return (
    <div className="container mx-auto flex items-center justify-center">
      <div className="w-full max-w-md space-y-8">
        <div className="text-center">
          <h2 className="display-text text-3xl">Register</h2>
          <p className="mt-2 text-muted-foreground">Create your account to get started</p>
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
              name="name"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Name</FormLabel>
                  <FormControl>
                    <Input placeholder="John Doe" {...field} disabled={loading} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />
            
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
            
            <FormField
              control={form.control}
              name="password_confirmation"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Confirm Password</FormLabel>
                  <FormControl>
                    <Input type="password" placeholder="••••••••" {...field} disabled={loading} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />
            
            <Button type="submit" className="w-full" disabled={loading}>
              {loading ? <LoadingText text="Creating account" /> : "Create Account"}
            </Button>
          </form>
        </Form>
        
        <div className="text-center text-sm text-muted-foreground mt-4">
          <p>Already have an account? <Link to="/login" className="text-primary hover:underline">Sign in</Link></p>
        </div>
      </div>
    </div>
  );
}