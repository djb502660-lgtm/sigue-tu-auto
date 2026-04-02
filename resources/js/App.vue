<script setup>
import { onMounted, reactive, ref } from 'vue';

const loading = ref(false);
const error = ref(null);
const success = ref(null);
const orders = ref([]);
const statuses = ref([]);

const chatOpen = ref(false);
const chatLoading = ref(false);
const chatInput = ref('');
const chatPhone = ref('');
const chatMessages = ref([
    {
        role: 'assistant',
        text: 'Hola, soy el asistente de Sigue tu Auto. Puedes consultar el estado de tu vehículo con tu folio, tu celular registrado o escribiendo "placa" y el número.',
    },
]);

const form = reactive({
    client: {
        name: '',
        phone: '',
        email: '',
    },
    vehicle: {
        brand: '',
        model: '',
        color: '',
        plate: '',
        vin: '',
        mileage: '',
    },
    entry_date: '',
    work_description: '',
    observations: '',
});

async function fetchOrders() {
    try {
        const { data } = await window.axios.get('/api/service-orders');
        orders.value = data.data ?? [];
    } catch {
        // silencioso
    }
}

async function fetchStatuses() {
    try {
        const { data } = await window.axios.get('/api/statuses');
        statuses.value = data;
    } catch {
        // silencioso
    }
}

async function submit() {
    loading.value = true;
    error.value = null;
    success.value = null;

    try {
        const response = await window.axios.post('/api/service-orders', form);
        success.value = `Orden creada. Folio: ${response.data.folio_number}`;
        form.work_description = '';
        form.observations = '';
        await fetchOrders();
    } catch (e) {
        error.value = 'No se pudo registrar la orden. Revisa los datos.';
    } finally {
        loading.value = false;
    }
}

async function sendChat() {
    const text = chatInput.value.trim();
    if (!text || chatLoading.value) {
        return;
    }
    chatMessages.value.push({ role: 'user', text });
    chatInput.value = '';
    chatLoading.value = true;
    try {
        const { data } = await window.axios.post('/api/chat', {
            message: text,
            phone: chatPhone.value.trim() || null,
        });
        chatMessages.value.push({ role: 'assistant', text: data.reply });
    } catch {
        chatMessages.value.push({
            role: 'assistant',
            text: 'No pude conectar con el servidor. Verifica tu conexión e intenta de nuevo.',
        });
    } finally {
        chatLoading.value = false;
    }
}

async function updateOrderStatus(order, event) {
    const statusId = event.target.value;
    if (!statusId) {
        return;
    }
    if (Number(statusId) === order.status_id) {
        return;
    }
    loading.value = true;
    error.value = null;
    try {
        await window.axios.post(`/api/service-orders/${order.id}/status`, {
            status_id: Number(statusId),
        });
        await fetchOrders();
        success.value = `Estado actualizado (${order.folio_number}). Si hay correo, se notificó al cliente.`;
        setTimeout(() => {
            success.value = null;
        }, 5000);
    } catch {
        error.value = 'No se pudo actualizar el estado.';
    } finally {
        loading.value = false;
    }
}

onMounted(async () => {
    await Promise.all([fetchOrders(), fetchStatuses()]);
});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-100 via-slate-50 to-slate-100">
        <!-- Cabecera -->
        <header class="bg-slate-800 text-white shadow-lg">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 py-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1-1V6a1 1 0 00-1-1h-1m-1 1a1 1 0 011-1h2a1 1 0 011 1v10a1 1 0 01-1 1h-1m-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold tracking-tight">Sigue tu Auto</h1>
                            <p class="text-slate-400 text-sm">Orden de Servicio · Instituto Superior Tecnológico</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        Mecánica Automotriz
                    </span>
                </div>
            </div>
        </header>

        <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
            <!-- Tarjeta principal del formulario -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden mb-8">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-lg font-semibold text-slate-800">Nueva orden de servicio</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Complete los datos del cliente y del vehículo.</p>
                </div>

                <form class="p-6 space-y-8" @submit.prevent="submit">
                    <!-- Datos del cliente -->
                    <section class="space-y-4">
                        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-1 h-4 rounded-full bg-emerald-500"></span>
                            Datos del cliente
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-slate-600">Nombre</label>
                                <input v-model="form.client.name" type="text" required
                                    class="input-field w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-slate-600">Celular</label>
                                <input v-model="form.client.phone" type="tel" required
                                    class="input-field w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-slate-600">Correo electrónico</label>
                                <input v-model="form.client.email" type="email"
                                    class="input-field w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                            </div>
                        </div>
                    </section>

                    <!-- Datos del vehículo -->
                    <section class="space-y-4">
                        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-1 h-4 rounded-full bg-slate-400"></span>
                            Datos del vehículo
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-slate-600">Marca</label>
                                <input v-model="form.vehicle.brand" type="text" required
                                    class="input-field w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-slate-600">Modelo</label>
                                <input v-model="form.vehicle.model" type="text" required
                                    class="input-field w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-slate-600">Color</label>
                                <input v-model="form.vehicle.color" type="text"
                                    class="input-field w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-slate-600">Placas</label>
                                <input v-model="form.vehicle.plate" type="text" required
                                    class="input-field w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-slate-600">Número de serie (VIN)</label>
                                <input v-model="form.vehicle.vin" type="text"
                                    class="input-field w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-slate-600">Kilometraje</label>
                                <input v-model="form.vehicle.mileage" type="number" min="0"
                                    class="input-field w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-slate-600">Fecha de ingreso</label>
                                <input v-model="form.entry_date" type="datetime-local"
                                    class="input-field w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                            </div>
                        </div>
                    </section>

                    <!-- Trabajo y observaciones -->
                    <section class="space-y-4">
                        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-1 h-4 rounded-full bg-amber-400"></span>
                            Trabajo a realizar
                        </h3>
                        <textarea v-model="form.work_description" rows="3"
                            class="input-field w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition resize-y min-h-[80px]"
                            placeholder="Describa el trabajo solicitado..."></textarea>
                    </section>

                    <section class="space-y-4">
                        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-1 h-4 rounded-full bg-slate-300"></span>
                            Observaciones
                        </h3>
                        <textarea v-model="form.observations" rows="2"
                            class="input-field w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition resize-y min-h-[64px]"
                            placeholder="Daños preexistentes, inventario, etc."></textarea>
                    </section>

                    <!-- Pie del formulario -->
                    <div class="pt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-t border-slate-100">
                        <div class="space-y-1 min-w-0">
                            <p v-if="success" class="text-sm font-medium text-emerald-700 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                {{ success }}
                            </p>
                            <p v-if="error" class="text-sm font-medium text-red-600">{{ error }}</p>
                            <p v-if="!success && !error" class="text-sm text-slate-500">
                                El cliente podrá consultar el avance por chatbot usando su celular.
                            </p>
                        </div>
                        <button type="submit" :disabled="loading"
                            class="btn-primary shrink-0 inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed transition shadow-sm hover:shadow">
                            <span v-if="!loading">Guardar orden</span>
                            <span v-else class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Guardando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Órdenes recientes -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <h2 class="text-lg font-semibold text-slate-800">Órdenes recientes</h2>
                    <p v-if="orders.length" class="text-sm text-slate-500">{{ orders.length }} orden(es)</p>
                </div>
                <div class="p-6">
                    <div v-if="!orders.length" class="text-center py-12 px-4">
                        <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="text-slate-500 font-medium">Aún no hay órdenes</p>
                        <p class="text-sm text-slate-400 mt-1">La primera que registres aparecerá aquí.</p>
                    </div>
                    <div v-else class="overflow-x-auto -mx-2">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-200">
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Folio</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Cliente</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Celular</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Vehículo</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Placas</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Estado</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600 min-w-[11rem]">Cambiar estado</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Ingreso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(order, i) in orders" :key="order.id"
                                    class="border-b border-slate-100 hover:bg-slate-50/80 transition"
                                    :class="i % 2 === 0 ? 'bg-white' : 'bg-slate-50/30'">
                                    <td class="px-4 py-3 font-medium text-slate-800">{{ order.folio_number }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ order.client?.name }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ order.client?.phone }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ order.vehicle?.brand }} {{ order.vehicle?.model }}</td>
                                    <td class="px-4 py-3 text-slate-600 font-medium">{{ order.vehicle?.plate }}</td>
                                    <td class="px-4 py-3">
                                        <span v-if="order.status"
                                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium"
                                            :class="order.status.slug === 'entregado'
                                                ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                                : 'bg-amber-50 text-amber-700 border border-amber-200'">
                                            {{ order.status.name }}
                                        </span>
                                        <span v-else class="text-slate-400 text-xs">Sin estado</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <select
                                            class="w-full max-w-[200px] text-xs rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500"
                                            :value="order.status_id ?? ''"
                                            :disabled="loading"
                                            @change="updateOrderStatus(order, $event)"
                                        >
                                            <option value="" disabled>Seleccionar…</option>
                                            <option
                                                v-for="s in statuses"
                                                :key="s.id"
                                                :value="s.id"
                                            >
                                                {{ s.name }}
                                            </option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 text-xs">{{ order.entry_date ?? '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <!-- Chatbot flotante -->
        <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-3">
            <transition name="fade">
                <div
                    v-if="chatOpen"
                    class="w-[min(100vw-2rem,22rem)] max-h-[min(70vh,28rem)] flex flex-col rounded-2xl shadow-2xl border border-slate-200/90 bg-white overflow-hidden"
                >
                    <div class="px-4 py-3 bg-slate-800 text-white flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-8 h-8 rounded-lg bg-emerald-500/25 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold truncate">Asistente virtual</p>
                                <p class="text-[11px] text-slate-400 truncate">Consulta el estado de tu orden</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="p-1.5 rounded-lg hover:bg-white/10 text-slate-300 shrink-0"
                            aria-label="Cerrar chat"
                            @click="chatOpen = false"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="px-3 py-2 border-b border-slate-100 bg-slate-50/80">
                        <label class="block text-[11px] font-medium text-slate-500 mb-1">Celular (opcional, si ya lo tienes)</label>
                        <input
                            v-model="chatPhone"
                            type="tel"
                            autocomplete="tel"
                            placeholder="Ej. 0991234567"
                            class="w-full text-xs rounded-lg border border-slate-200 px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500"
                        >
                    </div>
                    <div class="flex-1 overflow-y-auto p-3 space-y-3 bg-slate-50/40 min-h-[200px]">
                        <div
                            v-for="(m, idx) in chatMessages"
                            :key="idx"
                            class="flex"
                            :class="m.role === 'user' ? 'justify-end' : 'justify-start'"
                        >
                            <div
                                class="max-w-[92%] rounded-2xl px-3 py-2 text-sm leading-relaxed shadow-sm"
                                :class="m.role === 'user'
                                    ? 'bg-emerald-600 text-white rounded-br-md'
                                    : 'bg-white text-slate-800 border border-slate-100 rounded-bl-md'"
                            >
                                {{ m.text }}
                            </div>
                        </div>
                        <div v-if="chatLoading" class="flex justify-start">
                            <div class="bg-white border border-slate-100 rounded-2xl rounded-bl-md px-3 py-2 text-xs text-slate-500 flex items-center gap-2">
                                <svg class="animate-spin h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Pensando…
                            </div>
                        </div>
                    </div>
                    <form class="p-3 border-t border-slate-100 bg-white flex gap-2" @submit.prevent="sendChat">
                        <input
                            v-model="chatInput"
                            type="text"
                            class="flex-1 min-w-0 text-sm rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500"
                            placeholder="Escribe tu consulta…"
                            :disabled="chatLoading"
                        >
                        <button
                            type="submit"
                            class="shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50"
                            :disabled="chatLoading || !chatInput.trim()"
                            aria-label="Enviar"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </transition>
            <button
                type="button"
                class="inline-flex items-center justify-center w-14 h-14 rounded-full shadow-lg bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition"
                :class="chatOpen ? 'ring-2 ring-emerald-300 ring-offset-2' : ''"
                aria-label="Abrir chat"
                @click="chatOpen = !chatOpen"
            >
                <svg v-if="!chatOpen" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <svg v-else class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(8px);
}
</style>

