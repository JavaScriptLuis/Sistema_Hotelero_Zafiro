<template>
  <div class="showcase">
    <div class="overlay"></div>
    <video autoplay loop muted playsinline class="background-video">
      <source src="@/assets/fondouno4k.mp4" type="video/mp4">
    </video>
    <div class="content">
      <div class="text">
        <h3>RESIDENCIAL</h3>
        <h3>ZAFIRO</h3>
      </div>
      <div class="login-form-container">
        <form class="login-form" @submit.prevent="accionBoton">
          <img src="@/assets/logo.png" alt="Logo" class="logo"> <!-- Añadir la imagen del logo -->
          <h2>Iniciar la Sesión</h2>
          
          <div class="input-field">
            <span class="error-messages">{{ errors.email }}</span>
            <v-icon class="icon">mdi-email</v-icon>
            <input v-model="form.email" type="text" required>
            <label>Usuario</label>
          </div>
          <div class="input-field password-field">
            <span class="error-messages">{{ errors.password }}</span>
            <v-icon class="icon">mdi-lock</v-icon>
            <input v-model="form.password" :type="showPassword ? 'text' : 'password'" required>
            <label>Contraseña</label>
            <v-icon @click="togglePasswordVisibility" class="icon-eye">{{ showPassword ? 'mdi-eye' : 'mdi-eye-off' }}</v-icon>
          </div>
          <div class="checkbox-field">
            <input type="checkbox" v-model="form.remember">
            <label>Recordar contraseña</label>
          </div>
          <button type="submit">Ingresar</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import useAuth from '@/services/useAuth';
import { ref, reactive } from 'vue';

const { login, errors } = useAuth();
const showPassword = ref(false);
const form = reactive({
  email: '',
  password: '',
  remember: false // Añade esta línea para manejar el estado del checkbox
});
const accionBoton = () => {
  login(form).then(response => {
    if (form.remember) {
      // Guardar el token en localStorage para recordar al usuario
      localStorage.setItem('authToken', response.token);
    } else {
      // Asegurarse de que el token no esté en localStorage si el checkbox no está marcado
      localStorage.removeItem('authToken');
    }
  });
};
const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', sans-serif;
}

.showcase {
  position: relative;
  width: 100%;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #111;
  z-index: 2;
  padding: 20px;
}

.showcase video {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: 0.9;
}

.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(21, 98, 117, 0.6);
  mix-blend-mode: overlay;
}

.content {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  width: 100%;
  max-width: 1200px;
  z-index: 10;
  text-align: center;
}

.text {
  max-width: 600px;
  color: #fff;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
  padding: 20px;
  animation: fadeIn 1.5s ease-in-out;
}

.text h3 {
  font-size: 4em; /* Aumenta este valor para hacer el texto más grande */
  font-weight: 800;
  line-height: 1em;
  text-transform: uppercase;
  margin-bottom: 20px;
  animation: slideInLeft 1.5s ease-in-out;
  /*-webkit-text-stroke: 2px black; /* Añadir un borde negro */
}

.text p {
  font-size: 1.1em;
  margin: 20px 0;
  font-weight: 400;
  animation: slideInRight 1.5s ease-in-out;
}

.login-form-container {
  background: rgba(0, 0, 0, 0.8);
  padding: 50px 20px;
  border-radius: 10px;
  width: 100%;
  max-width: 750px;
  min-height: 570px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
  animation: fadeIn 2s ease-in-out;
}

.login-form {
  display: flex;
  flex-direction: column;
  align-items: center;
  color: #fff;
  
}

.login-form h2 {
  font-size: 50px;
  margin-bottom: 20px;
  
}

.logo {
  width: 150px; /* Ajusta el tamaño según tus necesidades */
  height: 150px;
  border-radius: 50%;
  margin-bottom: 40px; /* Añadir un margen inferior para separar la imagen del formulario */
}

.input-field {
  position: relative;
  margin-bottom: 25px;
  width: 100%;
  animation: slideInUp 1s ease-in-out;
}

.input-field .icon {
  position: absolute;
  left: 10px;
  top: 60%; /* Cambia este valor para mover el ícono más abajo */
  transform: translateY(-50%);
  color: #888;
  font-size: 1.5em;
}

.input-field .icon-eye {
  position: absolute;
  right: 10px;
  top: 63%; /* Cambia este valor para mover el ícono más abajo */
  transform: translateY(-50%);
  cursor: pointer;
  color: #888;
  font-size: 1.5em;
}

.input-field input {
  width: 100%;
  padding: 20px 20px 20px 45px;
  border: none;
  border-radius: 5px;
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  font-size: 1.2em;
}

.input-field label {
  position: absolute;
  top: 60%; /* Cambia este valor para mover el texto más abajo */
  left: 45px;
  transform: translateY(-50%);
  color: #fff;
  pointer-events: none;
  transition: all 0.3s ease;
  font-size: 1.2em;
}

.input-field input:focus + label,
.input-field input:valid + label {
  top: -10px;
  left: 45px;
  font-size: 12px;
}

.checkbox-field {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}

.checkbox-field input {
  margin-right: 10px;
}

button {
  width: 100%;
  padding: 15px;
  border: none;
  border-radius: 5px;
  background: #00bcd4;
  color: #fff;
  cursor: pointer;
  transition: background 0.3s ease;
  font-size: 1.2em;
}

button:hover {
  background: #0097a7;
}

.error-messages {
  color: #ff0000;
  padding: 5px;
  margin-top: 10px;
  border-radius: 8px;
  width: 100%;
  text-align: center;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideInLeft {
  from {
    transform: translateX(-100%);
  }
  to {
    transform: translateX(0);
  }
}

@keyframes slideInRight {
  from {
    transform: translateX(100%);
  }
  to {
    transform: translateX(0);
  }
}

@keyframes slideInUp {
  from {
    transform: translateY(100%);
  }
  to {
    transform: translateY(0);
  }
}

@media (max-width: 1200px) {
  .content {
    flex-direction: column;
  }

  .text {
    margin-bottom: 20px;
  }
}

@media (max-width: 768px) {
  .showcase {
    padding: 10px;
  }

  .text h3 {
    font-size: 2em;
  }

  .login-form-container {
    padding: 30px 10px;
  }

  .login-form h2 {
    font-size: 40px;
  }

  .logo {
    width: 120px; /* Ajusta el tamaño según tus necesidades */
    height: 120px;
  }
}

@media (max-width: 480px) {
  .text h3 {
    font-size: 1.5em;
  }

  .text p {
    font-size: 0.9em;
  }

  .login-form-container {
    padding: 20px 10px;
  }

  .login-form h2 {
    font-size: 30px;
  }

  .logo {
    width: 100px; /* Ajusta el tamaño según tus necesidades */
    height: 100px;
  }
}
</style>
