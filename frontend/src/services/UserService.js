import axios from 'axios';
import { Common } from '../utils/Common';

export class UserService {
    static userRoute = 'http://localhost:8081/whatsphant/users';
    static authRoute = 'http://localhost:8081/whatsphant/auth';

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

            Common.setCookie('WhatsphantJWT', data.token, 1);

            return true;
        } catch (error) {
            console.error(error);

            return false;
        }
    }
}