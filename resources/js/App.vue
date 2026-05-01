<script setup>
import { computed, onMounted, reactive, ref } from 'vue';

const loading = ref(false);
const error = ref(null);
const success = ref(null);
const orders = ref([]);
const statuses = ref([]);
const search = ref('');

const chatOpen = ref(false);
const chatLoading = ref(false);
const chatInput = ref('');
const chatPhone = ref('');
const chatMessages = ref([
    {
        role: 'assistant',
        text: 'Hola, soy el asistente de Sigue tu Auto. Puedes consultar el estado de tu vehiculo con tu folio, tu celular registrado o escribiendo "placa" y el numero.',
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

const filteredOrders = computed(() => {
    const term = search.value.trim().toLowerCase();
    if (!term) {
        return orders.value;
    }

    return orders.value.filter((order) => {
        const pool = [
            order.folio_number,
            order.client?.name,
            order.client?.phone,
            order.vehicle?.plate,
            `${order.vehicle?.brand ?? ''} ${order.vehicle?.model ?? ''}`,
            order.status?.name,
        ]
            .join(' ')
            .toLowerCase();

        return pool.includes(term);
    });
});

const dashboardStats = computed(() => {
    const total = orders.value.length;
    const delivered = orders.value.filter((order) => order.status?.slug === 'entregado').length;
    const inProgress = total - delivered;
    const withEmail = orders.value.filter((order) => Boolean(order.client?.email)).length;

    return {
        total,
        delivered,
        inProgress,
        withEmail,
    };
});

async function fetchOrders() {
    try {
        const { data } = await window.axios.get('/api/service-orders');
        orders.value = data.data ?? [];
    } catch (e) {
        if (e?.response?.status === 401) {
            error.value = 'Inicia sesion para consultar y administrar ordenes.';
            return;
        }
        error.value = 'No se pudieron cargar las ordenes.';
    }
}

async function fetchStatuses() {
    try {
        const { data } = await window.axios.get('/api/statuses');
        statuses.value = data;
    } catch {
        statuses.value = [];
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
        if (e?.response?.status === 401) {
            error.value = 'Debes iniciar sesion para crear ordenes.';
        } else {
            error.value = 'No se pudo registrar la orden. Revisa los datos.';
        }
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
            text: 'No pude conectar con el servidor. Verifica tu conexion e intenta de nuevo.',
        });
    } finally {
        chatLoading.value = false;
    }
}

async function updateOrderStatus(order, event) {
    const statusId = event.target.value;
    if (!statusId || Number(statusId) === order.status_id) {
        return;
    }

    loading.value = true;
    error.value = null;
    try {
        await window.axios.post(`/api/service-orders/${order.id}/status`, {
            status_id: Number(statusId),
        });
        await fetchOrders();
        success.value = `Estado actualizado (${order.folio_number}). Si hay correo, se notifico al cliente.`;
        setTimeout(() => {
            success.value = null;
        }, 5000);
    } catch (e) {
        if (e?.response?.status === 401) {
            error.value = 'Debes iniciar sesion para cambiar estados.';
        } else {
            error.value = 'No se pudo actualizar el estado.';
        }
    } finally {
        loading.value = false;
    }
}

function formatDate(value) {
    if (!value) {
        return '-';
    }

    const parsed = new Date(value);
    if (Number.isNaN(parsed.getTime())) {
        return value;
    }

    return parsed.toLocaleString('es-MX', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function statusPillClass(statusSlug) {
    if (statusSlug === 'entregado') {
        return 'bg-emerald-100 text-emerald-700 border-emerald-200';
    }

    if (statusSlug === 'en-reparacion' || statusSlug === 'en-pruebas') {
        return 'bg-sky-100 text-sky-700 border-sky-200';
    }

    return 'bg-amber-100 text-amber-700 border-amber-200';
}

onMounted(async () => {
    await Promise.all([fetchOrders(), fetchStatuses()]);
});
</script>

<template>
    <div class="min-h-screen bg-slate-100 text-slate-800">
        <header class="bg-slate-900 text-white border-b border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 sm:py-5 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1-1V6a1 1 0 00-1-1h-1m-1 1a1 1 0 011-1h2a1 1 0 011 1v10a1 1 0 01-1 1h-1m-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold">Sigue tu Auto</h1>
                        <p class="text-xs sm:text-sm text-slate-400">Consulta y gestion de ordenes de servicio</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-lg text-xs font-semibold bg-emerald-500/20 text-emerald-200 border border-emerald-500/30 shadow-sm">
                    Mecatronica Automotriz
                </span>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8 space-y-6">
            <section class="rounded-3xl bg-gradient-to-r from-emerald-600 via-emerald-700 to-slate-900 text-white p-6 sm:p-8 shadow-lg">
                <div class="grid lg:grid-cols-2 gap-6 items-center">
                    <div>
                        <p class="text-emerald-100 text-sm font-semibold tracking-wide uppercase">Plataforma de taller</p>
                        <h2 class="mt-2 text-2xl sm:text-3xl font-bold leading-tight">Diseno simple para registrar y consultar ordenes rapido</h2>
                        <p class="mt-3 text-emerald-50/90 text-sm sm:text-base">
                            Administra ingresos, monitorea estados y da seguimiento al cliente con una vista clara de dashboard y chatbot.
                        </p>
                    </div>
                    <div class="grid sm:grid-cols-3 gap-3">
                        <div class="rounded-2xl bg-white/15 border border-white/20 p-4">
                            <p class="text-xs text-emerald-100">Ordenes totales</p>
                            <p class="text-2xl font-bold mt-1">{{ dashboardStats.total }}</p>
                        </div>
                        <div class="rounded-2xl bg-white/15 border border-white/20 p-4">
                            <p class="text-xs text-emerald-100">En proceso</p>
                            <p class="text-2xl font-bold mt-1">{{ dashboardStats.inProgress }}</p>
                        </div>
                        <div class="rounded-2xl bg-white/15 border border-white/20 p-4">
                            <p class="text-xs text-emerald-100">Entregadas</p>
                            <p class="text-2xl font-bold mt-1">{{ dashboardStats.delivered }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid xl:grid-cols-5 gap-6">
                <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                        <h3 class="font-semibold text-slate-800">Nueva orden</h3>
                        <p class="text-sm text-slate-500">Registra cliente, vehiculo y detalle de trabajo</p>
                    </div>

                    <form class="p-5 space-y-5" @submit.prevent="submit">
                        <div class="grid gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Nombre del cliente</label>
                                <input v-model="form.client.name" type="text" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                            </div>
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Celular</label>
                                    <input v-model="form.client.phone" type="tel" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Correo</label>
                                    <input v-model="form.client.email" type="email" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>
                            </div>
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Marca</label>
                                    <input v-model="form.vehicle.brand" type="text" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Modelo</label>
                                    <input v-model="form.vehicle.model" type="text" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>
                            </div>
                            <div class="grid sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Placas</label>
                                    <input v-model="form.vehicle.plate" type="text" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Color</label>
                                    <input v-model="form.vehicle.color" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Kilometraje</label>
                                    <input v-model="form.vehicle.mileage" type="number" min="0" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">VIN</label>
                                <input v-model="form.vehicle.vin" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Fecha de ingreso</label>
                                <input v-model="form.entry_date" type="datetime-local" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Trabajo solicitado</label>
                                <textarea v-model="form.work_description" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm resize-y min-h-[84px] focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Observaciones</label>
                                <textarea v-model="form.observations" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm resize-y min-h-[64px] focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"></textarea>
                            </div>
                        </div>

                        <div class="border-t border-slate-100 pt-4 space-y-2">
                            <p v-if="success" class="text-sm text-emerald-700 font-medium">{{ success }}</p>
                            <p v-if="error" class="text-sm text-rose-700 font-medium">{{ error }}</p>
                            <button
                                type="submit"
                                :disabled="loading"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 text-white text-sm font-semibold px-4 py-3 hover:bg-emerald-700 disabled:opacity-60 shadow-sm hover:shadow"
                            >
                                <span v-if="loading">Guardando...</span>
                                <span v-else>Guardar orden</span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="xl:col-span-3 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="font-semibold text-slate-800">Dashboard de consulta</h3>
                            <p class="text-sm text-slate-500">Busca por folio, cliente, celular, placas o estado</p>
                        </div>
                        <div class="w-full sm:w-72">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Buscar orden..."
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                            >
                        </div>
                    </div>

                    <div class="px-5 py-3 border-b border-slate-100 grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                        <div class="rounded-xl bg-slate-50 border border-slate-100 py-2">
                            <p class="text-[11px] text-slate-500">Total</p>
                            <p class="text-lg font-bold text-slate-800">{{ dashboardStats.total }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 border border-slate-100 py-2">
                            <p class="text-[11px] text-slate-500">En proceso</p>
                            <p class="text-lg font-bold text-slate-800">{{ dashboardStats.inProgress }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 border border-slate-100 py-2">
                            <p class="text-[11px] text-slate-500">Entregadas</p>
                            <p class="text-lg font-bold text-emerald-700">{{ dashboardStats.delivered }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 border border-slate-100 py-2">
                            <p class="text-[11px] text-slate-500">Con correo</p>
                            <p class="text-lg font-bold text-slate-800">{{ dashboardStats.withEmail }}</p>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5">
                        <div v-if="!filteredOrders.length" class="text-center py-14 text-slate-500">
                            No hay ordenes que coincidan con la busqueda.
                        </div>

                        <div v-else class="space-y-3">
                            <div class="grid gap-3 md:hidden">
                                <article
                                    v-for="order in filteredOrders"
                                    :key="order.id"
                                    class="rounded-xl border border-slate-200 bg-white p-4 space-y-3 shadow-sm"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-xs text-slate-500">Folio</p>
                                            <p class="font-semibold text-slate-800">{{ order.folio_number }}</p>
                                        </div>
                                        <span
                                            v-if="order.status"
                                            class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold border"
                                            :class="statusPillClass(order.status.slug)"
                                        >
                                            {{ order.status.name }}
                                        </span>
                                        <span v-else class="text-xs text-slate-400">Sin estado</span>
                                    </div>
                                    <div class="text-sm text-slate-700">
                                        <p class="font-medium">{{ order.client?.name ?? '-' }}</p>
                                        <p class="text-xs text-slate-500">{{ order.client?.phone ?? '-' }}</p>
                                        <p class="mt-1">{{ order.vehicle?.brand }} {{ order.vehicle?.model }}</p>
                                        <p class="text-xs text-slate-500">{{ order.vehicle?.plate ?? '-' }}</p>
                                    </div>
                                    <div class="grid gap-2">
                                        <label class="text-xs font-semibold text-slate-500">Actualizar estado</label>
                                        <select
                                            :value="order.status_id ?? ''"
                                            :disabled="loading"
                                            class="w-full rounded-lg border border-slate-200 px-2 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                                            @change="updateOrderStatus(order, $event)"
                                        >
                                            <option value="" disabled>Seleccionar...</option>
                                            <option v-for="status in statuses" :key="status.id" :value="status.id">
                                                {{ status.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <p class="text-xs text-slate-500">Ingreso: {{ formatDate(order.entry_date) }}</p>
                                </article>
                            </div>

                            <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200 text-slate-600">
                                        <th class="px-3 py-3 text-left font-semibold">Folio</th>
                                        <th class="px-3 py-3 text-left font-semibold">Cliente</th>
                                        <th class="px-3 py-3 text-left font-semibold">Vehiculo</th>
                                        <th class="px-3 py-3 text-left font-semibold">Estado</th>
                                        <th class="px-3 py-3 text-left font-semibold min-w-[10rem]">Actualizar</th>
                                        <th class="px-3 py-3 text-left font-semibold">Ingreso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(order, index) in filteredOrders"
                                        :key="order.id"
                                        :class="index % 2 === 0 ? 'bg-white' : 'bg-slate-50/60'"
                                        class="border-b border-slate-100 hover:bg-emerald-50/40"
                                    >
                                        <td class="px-3 py-3 font-semibold text-slate-800">{{ order.folio_number }}</td>
                                        <td class="px-3 py-3 text-slate-700">
                                            <p class="font-medium">{{ order.client?.name ?? '-' }}</p>
                                            <p class="text-xs text-slate-500">{{ order.client?.phone ?? '-' }}</p>
                                        </td>
                                        <td class="px-3 py-3 text-slate-700">
                                            <p>{{ order.vehicle?.brand }} {{ order.vehicle?.model }}</p>
                                            <p class="text-xs text-slate-500">{{ order.vehicle?.plate ?? '-' }}</p>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span
                                                v-if="order.status"
                                                class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold border"
                                                :class="statusPillClass(order.status.slug)"
                                            >
                                                {{ order.status.name }}
                                            </span>
                                            <span v-else class="text-xs text-slate-400">Sin estado</span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <select
                                                :value="order.status_id ?? ''"
                                                :disabled="loading"
                                                class="w-full rounded-lg border border-slate-200 px-2 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                                                @change="updateOrderStatus(order, $event)"
                                            >
                                                <option value="" disabled>Seleccionar...</option>
                                                <option v-for="status in statuses" :key="status.id" :value="status.id">
                                                    {{ status.name }}
                                                </option>
                                            </select>
                                        </td>
                                        <td class="px-3 py-3 text-xs text-slate-600">{{ formatDate(order.entry_date) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="px-3 py-2 border-b border-slate-100 bg-slate-50/80">
                        <label class="block text-[11px] font-medium text-slate-500 mb-1">Celular (opcional)</label>
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
                            v-for="(message, idx) in chatMessages"
                            :key="idx"
                            class="flex"
                            :class="message.role === 'user' ? 'justify-end' : 'justify-start'"
                        >
                            <div
                                class="max-w-[92%] rounded-2xl px-3 py-2 text-sm leading-relaxed shadow-sm"
                                :class="message.role === 'user'
                                    ? 'bg-emerald-600 text-white rounded-br-md'
                                    : 'bg-white text-slate-800 border border-slate-100 rounded-bl-md'"
                            >
                                {{ message.text }}
                            </div>
                        </div>
                        <div v-if="chatLoading" class="flex justify-start">
                            <div class="bg-white border border-slate-100 rounded-2xl rounded-bl-md px-3 py-2 text-xs text-slate-500 flex items-center gap-2">
                                <svg class="animate-spin h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                                Pensando...
                            </div>
                        </div>
                    </div>
                    <form class="p-3 border-t border-slate-100 bg-white flex gap-2" @submit.prevent="sendChat">
                        <input
                            v-model="chatInput"
                            type="text"
                            class="flex-1 min-w-0 text-sm rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500"
                            placeholder="Escribe tu consulta..."
                            :disabled="chatLoading"
                        >
                        <button
                            type="submit"
                            class="shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50"
                            :disabled="chatLoading || !chatInput.trim()"
                            aria-label="Enviar"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <svg v-else class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
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

