export class UserModel {
    id: string;
    name: string;
    email: string;
    public get initial() : string {
        return this.name.split(' ').slice(0, 2).map((word) => word.charAt(0)).join('');
    }

    constructor(
        id: string,
        name: string,
        email: string,
    ) {
        this.id = id;
        this.name = name;
        this.email = email;
        
    }

    static fromJson(json: any): UserModel {
        if (!json.id) {
            throw new Error("User ID is required");
        }
        if (!json.name) {
            throw new Error("Name is required");
        }
        if (!json.email) {
            throw new Error("Email is required");
        }

        const user = new UserModel(
            json.id,
            json.name,
            json.email,
        );
        return user;
    }
}