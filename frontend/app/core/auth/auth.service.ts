import { UserModel } from "@/users/user.model";
import api from "../http/api";
import { User } from "lucide-react";

interface Credentials {
    email: string;
    password: string;
}

interface SignupData {
    name: string;
    email: string;
    password: string;
}

export const getCurrentUser = async (): Promise<UserModel | null> => {
    return await api.get("/profile")
        .then((response) => UserModel.fromJson(response.data.user))
        .catch((error) => {
            console.error("Error getting current user", error);
            return null;
        });
};

export const isLoggedIn = async (): Promise<boolean> => {
    return await getCurrentUser().then((user: UserModel|null) => user !== null);
};

export const login = async (credentials: Credentials): Promise<UserModel> => {
    return new Promise((resolve, reject) => {
        api.post("/login", credentials)
            .then( async () => (await getCurrentUser()) )
            .then(resolve)
            .catch(
                (error) => {
                    if (error.response?.status === 401){
                        return reject({ message: "Invalid credentials", cause: { name: "InvalidCredentialsError" } });
                    }

                    if (error.response?.status === 422){
                        return reject({ message: error.response.data.message, cause: { name: "UnprocessableEntity" } });
                    }

                    throw error;
                }
            );
    });
}

export const signup = async (signupData: SignupData): Promise<UserModel> => {
    return new Promise((resolve, reject) => {

        api.post("/register", {...signupData, password_confirmation: signupData.password})
            .then(async () => resolve(await login({ email: signupData.email, password: signupData.password })))
            .catch(
                (error) => {
                    if (error.response?.status === 422){
                        return reject({ message: error.response.data.message, cause: { name: "UnprocessableEntity" } });
                    }

                    throw error;
                }
            );
    });
}

export const logout = (): Promise<boolean> => {
    return new Promise((resolve) => {
        api.delete("/logout").then(() => resolve(true));
    });
}

export default {
    login,
    signup,
    logout,
    getCurrentUser,
    isLoggedIn,
}