import { defineStore } from "pinia";
export const useAuthStore = defineStore('auth', {
    state: () =>
    ({
        token: localStorage.getItem('token') || null,
        user: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null,
        personal: localStorage.getItem('personal') ? JSON.parse(localStorage.getItem('personal')) : null
    }),
    getters: {
        isAuthenticated: (state) => !!state.token,
        userRole: (state) => state.user ? state.user.rol : null,
    },
    actions: {
        setPersonal(personal) {
            this.personal = personal
            localStorage.setItem('personal', JSON.stringify(personal));
        },
        setUser(user) {
            this.user = user
            localStorage.setItem('user', JSON.stringify(user));
        },
        login(token) {
            this.token = token;
            localStorage.setItem('token', token);
        },
        logout() {
            this.token = null;
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            localStorage.removeItem('personal');
        },
        initializeAuth() {
            const token = localStorage.getItem('token');
            const user = localStorage.getItem('user');
            if (token && user) {
                this.token = token;
                this.user = JSON.parse(user);
            }
        }
    }
})