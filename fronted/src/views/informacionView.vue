<template>
  <div class="acerca-container">
    <!-- Sidebar para el perfil -->
    <div class="perfil">
      <div class="perfil-imagen">
        <img
          class="rounded-circle"
          :src="imagenPerfil"
          alt="Luis Alejandro Callisaya Flores"
        />
      </div>
      <h2>Luis Alejandro Callisaya Flores</h2>
      <p>
        Justo stet no accusam stet invidunt sanctus magna clita vero eirmod, sit
        sit labore dolores lorem. Lorem at sit dolor dolores sed diam justo.
      </p>

      <!-- Sección de iconos -->
      <div class="redes-sociales">
        <a href="https://twitter.com" target="_blank" class="social-icon">
          <i class="fab fa-twitter"></i>
        </a>
        <a href="https://github.com" target="_blank" class="social-icon">
          <i class="fab fa-github"></i>
        </a>
        <a href="https://facebook.com" target="_blank" class="social-icon">
          <i class="fab fa-facebook"></i>
        </a>
      </div>
    </div>

    <!-- Información del sistema -->
    <div class="informacion-sistema">
      <h1>Acerca del sistema</h1>
      <p>
        Este sistema fue desarrollado con el propósito de ofrecer una solución
        eficiente y moderna a las necesidades de la empresa.
      </p>
      <div class="informacion-detalle">
        <div>
          <h4>Versión Actual</h4>
          <p>Frontend: Versión 1.3.0</p>
          <p>Backend: Versión 1.2.0</p>
        </div>
        <div>
          <h4>Tecnologías</h4>
          <p>Node.js versión 18 (backend)</p>
          <p>Angular 15 (frontend)</p>
        </div>
        <div>
          <h4>Contacto de soporte</h4>
          <p>Nombre: Yessenia Villarte Vaca</p>
          <p>Email: yesseniavillarte@gmail.com</p>
          <p>Teléfono: (+591) 753 15 804</p>
        </div>
      </div>
      <div class="botones">
        <!-- Botón para descargar el PDF -->
        <v-btn @click="descargarPDF" color="primary">
          <i class="fas fa-download"></i> Descargar Guía de Usuario (PDF)
        </v-btn>
        <!-- Botón para volver al sistema -->
        <v-btn @click="volverAlSistema" color="success">
          <i class="fas fa-arrow-left"></i> Volver al Sistema
        </v-btn>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      // URL de la imagen de perfil
      imagenPerfil:
        "https://media.istockphoto.com/id/1143956310/photo/young-brunette-boy-sitting-in-front-of-a-yellow-background-wearing-a-black-sweater-putting.jpg?s=612x612&w=is&k=20&c=roU4mNfAV-jYmBcPbfcnRn-Q0eFy7s4k636Uu-Jswpg=",
    };
  },
  methods: {
    // Método para descargar el PDF
    descargarPDF() {
      // Redirige a la ruta en Laravel que maneja la descarga del PDF
      window.location.href = 'http://localhost:8000/manual-usuario'; // Cambia la URL si es necesario
    },
    // Método para volver al sistema
    volverAlSistema() {
      this.$router.push("/inicio"); // Ajusta la ruta según la configuración de tu aplicación
    },
  },
};
</script>

<style scoped>
.acerca-container {
  display: flex;
  justify-content: space-between;
  padding: 20px;
  background-color: #f8f9fa;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.perfil {
  width: 30%;
  text-align: center;
}

.perfil-imagen img {
  width: 150px;
  height: 150px;
  object-fit: cover;
}

.perfil h2 {
  font-size: 24px;
  margin-top: 10px;
  color: #333;
}

.perfil p {
  font-size: 14px;
  margin: 10px 0;
}

.redes-sociales {
  margin-top: 15px;
}

.social-icon {
  margin: 0 10px;
  font-size: 24px;
  color: #333;
  transition: color 0.3s ease;
}

.social-icon:hover {
  color: #1a73e8;
}

.informacion-sistema {
  width: 65%;
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.informacion-sistema h1 {
  font-size: 28px;
  color: #3949ab;
}

.informacion-sistema p {
  font-size: 16px;
  color: #555;
}

.informacion-detalle {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
}

.informacion-detalle div {
  width: 30%;
}

.informacion-detalle h4 {
  font-size: 18px;
  color: #3949ab;
}

.botones {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
}

v-btn {
  font-size: 16px;
  padding: 10px 20px;
}
</style>