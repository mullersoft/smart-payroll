import { createApp } from "vue";
import { createPinia } from "pinia";
import router from "./router";
import App from "./App.vue";
import "./assets/style.css";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
const app = createApp(App);

app.use(createPinia());
app.use(router);
app.use(Toast, {
  position: "top-right",
  timeout: 4000,
  closeOnClick: true,
});

app.mount("#app");
