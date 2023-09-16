import axios from 'axios';

export class UserService {
    userRoute = 'http://localhost:8081/whatsphant/users';
    authRoute = 'http://localhost:8081/whatsphant/auth';

    static async login(email, password) {
        try {
            const { data } = await axios.post(UserService.authRoute, {
                email,
                password
            }, {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            console.log(data);
        } catch (error) {
            console.error(error);
        }
    }
}