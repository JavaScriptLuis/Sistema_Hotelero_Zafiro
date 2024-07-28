import { reactive } from 'vue';
import { useAuthStore } from '@/stores/useAuthStore';
import { http } from '@/services/httpService';
import { useRouter } from 'vue-router';

export default function useAuth() {
    const store = useAuthStore();
    const errors = reactive({
        email: '',
        password: ''
    });
    const router = useRouter();

    const login = async (data) => {
        try {
            let response = await http().post('/auth/login', data);
            store.login(response.data.access_token);
            router.push({ name: 'inicio' });
            store.setUser(response.data.user);
            store.setPersonal(response.data.personal);
        } catch (error) {
            errors.email = error.response.data.data.email || '';
            errors.password = error.response.data.data.password || '';
            console.log(error.response.data);
        }
    };

    const logout = async () => {
        try {
            await http().get('/auth/logout');
            store.logout();
            router.push({ name: 'login' });
        } catch (error) {
            console.log(error);
        }
    };

    return {
        login,
        logout,
        errors,
        isAuthenticated: store.isAuthenticated,
    };
}
