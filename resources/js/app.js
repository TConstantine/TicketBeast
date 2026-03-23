import './bootstrap';
import { createApp } from 'vue';
import TicketCheckout from './components/TicketCheckout.vue';

const app = createApp({
    compilerOptions: {}
});

app.component('ticket-checkout', TicketCheckout);
app.mount('#app');