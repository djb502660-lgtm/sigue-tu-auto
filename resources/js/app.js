import './bootstrap';
import { createApp } from 'vue';

const App = {
    data() {
        return {
            loading: false,
            error: null,
            success: null,
            orders: [],
            statuses: [],
            form: {
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
            },
        };
    },
    async mounted() {
        await Promise.all([this.fetchOrders(), this.fetchStatuses()]);
    },
    methods: {
        async fetchOrders() {
            try {
                const { data } = await window.axios.get('/api/service-orders');
                this.orders = data.data ?? [];
            } catch {
                // silencioso por ahora
            }
        },
        async fetchStatuses() {
            try {
                const { data } = await window.axios.get('/api/statuses');
                this.statuses = data;
            } catch {
                // silencioso por ahora
            }
        },
        async submit() {
            this.loading = true;
            this.error = null;
            this.success = null;

            try {
                const response = await window.axios.post('/api/service-orders', this.form);
                this.success = `Orden creada. Folio: ${response.data.folio_number}`;
                this.form.work_description = '';
                this.form.observations = '';
                await this.fetchOrders();
            } catch (e) {
                this.error = 'No se pudo registrar la orden. Revisa los datos.';
            } finally {
                this.loading = false;
            }
        },
    },
    template: `
    <main class="min-h-screen flex items-center justify-center p-4 bg-slate-100">
      <div class="w-full max-w-6xl bg-white shadow-xl rounded-xl p-8 space-y-6">
        <header class="border-b pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
          <div>
            <h1 class="text-2xl font-semibold text-slate-800">Orden de Servicio</h1>
            <p class="text-sm text-slate-500">
              Mecánica Automotriz - Gestión y seguimiento del mantenimiento vehicular
            </p>
          </div>
          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
            Sigue tu Auto
          </span>
        </header>

        <form class="space-y-6" @submit.prevent="submit">
          <section>
            <h2 class="text-sm font-semibold text-slate-700 mb-3 uppercase tracking-wide">Datos del cliente</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Nombre</label>
                <input v-model="form.client.name" type="text" class="w-full rounded-md border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Celular</label>
                <input v-model="form.client.phone" type="tel" class="w-full rounded-md border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Correo electrónico</label>
                <input v-model="form.client.email" type="email" class="w-full rounded-md border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
              </div>
            </div>
          </section>

          <section>
            <h2 class="text-sm font-semibold text-slate-700 mb-3 uppercase tracking-wide">Datos del vehículo</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Marca</label>
                <input v-model="form.vehicle.brand" type="text" class="w-full rounded-md border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Modelo</label>
                <input v-model="form.vehicle.model" type="text" class="w-full rounded-md border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Color</label>
                <input v-model="form.vehicle.color" type="text" class="w-full rounded-md border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Placas</label>
                <input v-model="form.vehicle.plate" type="text" class="w-full rounded-md border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Número de serie (VIN)</label>
                <input v-model="form.vehicle.vin" type="text" class="w-full rounded-md border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Kilometraje</label>
                <input v-model="form.vehicle.mileage" type="number" min="0" class="w-full rounded-md border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Fecha de ingreso</label>
                <input v-model="form.entry_date" type="datetime-local" class="w-full rounded-md border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
              </div>
            </div>
          </section>

          <section>
            <h2 class="text-sm font-semibold text-slate-700 mb-3 uppercase tracking-wide">Trabajo a realizar</h2>
            <textarea v-model="form.work_description" rows="3" class="w-full rounded-md border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Describa el trabajo solicitado..."></textarea>
          </section>

          <section>
            <h2 class="text-sm font-semibold text-slate-700 mb-3 uppercase tracking-wide">Observaciones</h2>
            <textarea v-model="form.observations" rows="2" class="w-full rounded-md border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Anote daños preexistentes, inventario, etc."></textarea>
          </section>

          <footer class="pt-4 border-t flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
              <p v-if="success" class="text-sm text-emerald-700 font-medium">{{ success }}</p>
              <p v-if="error" class="text-sm text-red-600 font-medium">{{ error }}</p>
              <p v-if="!success && !error" class="text-xs text-slate-500">
                Después conectaremos esta orden con el chatbot para que el cliente pueda consultar el avance usando su número de celular.
              </p>
            </div>
            <button type="submit" :disabled="loading" class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 disabled:cursor-not-allowed">
              <span v-if="!loading">Guardar orden</span>
              <span v-else>Guardando...</span>
            </button>
          </footer>
        </form>

        <section class="pt-4 border-t">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">
              Órdenes recientes
            </h2>
            <p class="text-xs text-slate-500" v-if="orders.length">
              {{ orders.length }} orden(es) registradas recientemente.
            </p>
          </div>
          <div v-if="!orders.length" class="text-xs text-slate-500">
            Aún no hay órdenes registradas. La primera que registres aparecerá aquí.
          </div>
          <div v-else class="overflow-x-auto">
            <table class="min-w-full text-xs">
              <thead>
                <tr class="bg-slate-50 text-slate-600">
                  <th class="px-3 py-2 text-left font-semibold">Folio</th>
                  <th class="px-3 py-2 text-left font-semibold">Cliente</th>
                  <th class="px-3 py-2 text-left font-semibold">Celular</th>
                  <th class="px-3 py-2 text-left font-semibold">Vehículo</th>
                  <th class="px-3 py-2 text-left font-semibold">Placas</th>
                  <th class="px-3 py-2 text-left font-semibold">Estado</th>
                  <th class="px-3 py-2 text-left font-semibold">Ingreso</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="order in orders" :key="order.id" class="border-t hover:bg-slate-50">
                  <td class="px-3 py-2 font-medium text-slate-800">{{ order.folio_number }}</td>
                  <td class="px-3 py-2">{{ order.client?.name }}</td>
                  <td class="px-3 py-2">{{ order.client?.phone }}</td>
                  <td class="px-3 py-2">
                    {{ order.vehicle?.brand }} {{ order.vehicle?.model }}
                  </td>
                  <td class="px-3 py-2">{{ order.vehicle?.plate }}</td>
                  <td class="px-3 py-2">
                    <span v-if="order.status" class="inline-flex items-center px-2 py-0.5 rounded-full border text-[11px]"
                          :class="order.status.slug === 'entregado' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-amber-50 border-amber-200 text-amber-700'">
                      {{ order.status.name }}
                    </span>
                    <span v-else class="text-slate-400">Sin estado</span>
                  </td>
                  <td class="px-3 py-2">{{ order.entry_date ?? '-' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </main>
  `,
};

createApp(App).mount('#app');
