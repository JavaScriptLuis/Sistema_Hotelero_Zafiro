import { defineStore } from "pinia";


export const useServerStore = defineStore('server', {

    state: () => ({
        apiURL: "http://127.0.0.1:8000/api",
        serverPath: "http://127.0.0.1:8000/",
    })
});