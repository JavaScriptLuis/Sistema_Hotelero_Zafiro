<template>
    <v-layout>
        <v-app-bar class="custom-app-bar" elevation="4">
            <v-app-bar-nav-icon @click.stop="toggleDrawer" color="black"></v-app-bar-nav-icon>
            <v-toolbar-title color="white" class="text-h5 font-weight-bold">
                Bienvenido {{ authStore.personal.nombre }} {{ authStore.personal.paterno }} {{ authStore.personal.materno }}
            </v-toolbar-title>
            <v-spacer></v-spacer>

            <!-- Botón de información -->
        <v-btn @click="goToInfo" class="mr-4" icon color="white">
          <v-icon>mdi-information</v-icon> <!-- Ícono de información -->
        </v-btn>

            <v-btn @click="cerrarSesion" class="mr-6" icon color="black" style="font-weight: bold;">
                Salir
            </v-btn>
        </v-app-bar>

        <v-navigation-drawer v-model="drawer" :rail="isRail" @update:rail="handleRailUpdate" class="custom-drawer"
            :permanent="$vuetify.display.mdAndUp">
            <div class="drawer-header">
                <v-avatar size="120" class="mb-4 elevation-4">
                    <v-img :src="detalleURL(authStore.personal.foto)" :alt="authStore.personal.nombre">
                        <template v-slot:placeholder>
                            <v-icon size="80" color="grey lighten-2">mdi-account</v-icon>
                        </template>
                    </v-img>
                </v-avatar>
                <div v-if="authStore.user.rol === 'admin'">
                    <h2 v-if="!isRail" class="text-subtitle-1 white--text">Panel Administrativo</h2>
                </div>
                <div v-else>
                    <h2 v-if="!isRail" class="text-subtitle-1 white--text">Recepción</h2>
                </div>
            </div>

            <v-list nav dense>
                <v-list-item v-for="(item, i) in filteredRoutes" :key="i" :to="item.route" active-class="active-item">
                    <template v-slot:prepend>
                        <v-icon :icon="item.icon"></v-icon>
                    </template>
                    <v-list-item-title>{{ item.text }}</v-list-item-title>
                </v-list-item>

                <v-list-group value="Punto de venta">
                    <template v-slot:activator="{ props }">
                        <v-list-item v-bind="props" prepend-icon="mdi-cash-register">
                            <v-list-item-title style="font-size: 16px;">Punto de Venta</v-list-item-title>
                        </v-list-item>
                    </template>
                    <v-list-item v-for="(item, i) in filteredSubMenu" :key="i" :to="item.route"
                        active-class="active-sub-item" class="sub-menu-item">
                        <template v-slot:prepend>
                            <v-icon size="small" class="mr-2">mdi-circle-small</v-icon>
                        </template>
                        <v-list-item-title class="text-wrap">{{ item.text }}</v-list-item-title>
                    </v-list-item>
                </v-list-group>

                <v-list-group value="Configuracion">
                    <template v-slot:activator="{ props }">
                        <v-list-item v-bind="props" prepend-icon="mdi-cog">
                            <v-list-item-title style="font-size: 16px;">Configuración</v-list-item-title>
                        </v-list-item>
                    </template>
                    <v-list-item v-for="(item, i) in filteredSubMenuConf" :key="i" :to="item.route"
                        active-class="active-sub-item" class="sub-menu-item">
                        <template v-slot:prepend>
                            <v-icon size="small" class="mr-2">mdi-circle-small</v-icon>
                        </template>
                        <v-list-item-title class="text-wrap">{{ item.text }}</v-list-item-title>
                    </v-list-item>
                </v-list-group>

                <v-list-group value="Reportes">
                    <template v-slot:activator="{ props }">
                        <v-list-item v-bind="props" prepend-icon="mdi-chart-box">
                            <v-list-item-title style="font-size: 16px;">Reportes</v-list-item-title>
                        </v-list-item>
                    </template>
                    <v-list-item v-for="(item, i) in filteredSubMenuReportes" :key="i" :to="item.route"
                        active-class="active-sub-item" class="sub-menu-item">
                        <template v-slot:prepend>
                            <v-icon size="small" class="mr-2">mdi-circle-small</v-icon>
                        </template>
                        <v-list-item-title class="text-wrap">{{ item.text }}</v-list-item-title>
                    </v-list-item>
                </v-list-group>

            </v-list>
        </v-navigation-drawer>

        <v-main class="custom-main">
            <v-container fluid>
                <router-view></router-view>
            </v-container>
        </v-main>
    </v-layout>

    <v-overlay :model-value="overlay" class="align-center justify-center">
        <v-progress-circular color="primary" indeterminate size="64"></v-progress-circular>
    </v-overlay>
</template>

<script setup>
import { ref, computed } from 'vue';
import useAuth from '@/services/useAuth';
import { useAuthStore } from '@/stores/useAuthStore';
import { useServerStore } from '@/stores/useService';
import router from '@/router';
import { useRouter } from 'vue-router';



// Función para redirigir a la vista de información
const goToInfo = () => {
    router.push({ name: 'informacion' }); // Redirigir a la vista información
  };

const authStore = useAuthStore();
const userRole = ref(authStore.userRole);
const serve = useServerStore();
const { logout } = useAuth();

const overlay = ref(false);
const drawer = ref(true);

const isRail = ref(false);

const toggleDrawer = () => {
    isRail.value = !isRail.value;
};

const handleRailUpdate = (val) => {
    isRail.value = val;
};
const filterRoutesByRole = (routes) => {
    return routes.filter(route => route.roles.includes(userRole.value));
};
const filteredRoutes = computed(() => filterRoutesByRole(routes.value));
const filteredSubMenu = computed(() => filterRoutesByRole(subMenu.value));
const filteredSubMenuConf = computed(() => filterRoutesByRole(subMenuConf.value));
const filteredSubMenuReportes = computed(() => filterRoutesByRole(subMenuReportes.value));
const routes = ref([
    { text: 'Inicio', icon: 'mdi-home', route: '/inicio', roles: ['admin', 'recep'] },
    { text: 'Personal', icon: 'mdi-account', route: '/personales', roles: ['admin'] },
    { text: 'Reserva', icon: 'mdi-calendar-check', route: '/reservas', roles: ['admin', 'recep'] },
    { text: 'Recepción', icon: 'mdi-desk', route: '/recepcion', roles: ['admin', 'recep'] },
    { text: 'Verificación de salidas', icon: 'mdi-exit-to-app', route: '/salidas', roles: ['admin', 'recep'] },
    { text: 'Clientes', icon: 'mdi-account', route: '/clientes', roles: ['admin', 'recep'] },
]);

const subMenu = ref([
    { text: 'Vender productos', route: '/vender-productos', roles: ['admin', 'recep'] },
    { text: 'Catalogo de productos', route: '/productos', roles: ['admin'] },
]);

const subMenuConf = ref([
    { text: 'Habitaciones', route: '/habitaciones', roles: ['admin'] },
    { text: 'Pisos', route: '/pisos', roles: ['admin'] },
    { text: 'Sucursales', route: '/sucursales', roles: ['admin'] },
    { text: 'Tipo habitacion', route: '/tipo-habitaciones', roles: ['admin'] },
]);

const subMenuReportes = ref([
    { text: 'Reporte de usuarios', route: '/usuario-reportes', roles: ['admin'] },
])
const cerrarSesion = async () => { 
    await logout();
}
const detalleURL = (archivo) => {
    return `${serve.serverPath}storage/fotos/${archivo}`;
}
</script>

<style scoped>
.custom-app-bar {
    background: linear-gradient(to right, #d9d9d9, #0059ff);
}

.custom-drawer {
    background: linear-gradient(to bottom, #d9d9d9,#0059ff);
}

.drawer-header {
    padding: 16px;
    text-align: center;
    background: rgba(255, 255, 255, 0.1);
}

.v-list-item {
    border-radius: 0 50px 50px 0;
    margin: 8px 16px 8px 0;
}

.active-item {
    background: linear-gradient(to right, #ff4081, #f50057);
    color: white;
}

.active-sub-item {
    background: rgba(255, 255, 255, 0.1);
}

.custom-main {
    background-image: url('@/assets/zafiro.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    min-height: 100vh; /* Asegura que el mínimo alto sea el de la ventana del navegador */
    width: 100%; /* Ocupa el ancho completo */
}

.v-navigation-drawer--rail {
    .v-list-item__prepend {
        margin-inline-end: 0;
    }

    .v-list-item__append {
        margin-inline-start: 0;
    }
}

.sub-menu-item {
    padding-left: 16px !important;
    min-height: 40px !important;
}

.sub-menu-item .v-list-item__content {
    padding-left: 8px;
}

.text-wrap {
    white-space: normal;
    word-break: break-word;
}
.custom-drawer .v-list-item-title {
    font-size: 16px; /* Cambia este valor al tamaño que prefieras */
    
}
.custom-drawer .sub-menu-item .v-list-item-title {
    font-size: 16px; /* Cambia este valor al tamaño que prefieras */
 
}
</style>