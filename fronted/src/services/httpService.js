import axios from "axios";

import { useServerStore } from "@/stores/useService";
import { useAuthStore } from "@/stores/useAuthStore";

export function http() {
    const authStore = useServerStore();
    const auth = useAuthStore();
    return axios.create({
        baseURL: authStore.apiURL,
        headers: {
            Authorization: `Bearer ${auth.token}`
        }
    });
}

