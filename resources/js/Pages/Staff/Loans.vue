<template>
    <div class="min-h-screen bg-gray-50 px-6 py-8">
        <div class="max-w-6xl mx-auto flex flex-col gap-6">
            <h1
                class="text-2xl font-serif font-semibold text-gray-800 tracking-tight"
            >
                Pozajmice
            </h1>

            <div class="bg-white rounded-2xl shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-max w-full">
                    <thead
                        class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider"
                    >
                        <tr>
                            <th class="px-6 py-3 text-left">Korisnik</th>
                            <th class="px-6 py-3 text-left">Knjiga</th>
                            <th class="px-6 py-3 text-left">Datum</th>
                            <th class="px-6 py-3 text-left">Rok vraćanja</th>
                            <th class="px-6 py-3 text-left">Dostava</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Akcije</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="loan in loans.data"
                            :key="loan.loanId"
                            class="hover:bg-gray-50"
                        >
                            <td
                                class="px-6 py-4 text-sm font-medium text-gray-800"
                            >
                                {{ loan.user?.name }} {{ loan.user?.surname }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ loan.book?.title }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ loan.loanDate }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ loan.endReturnDate }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{
                                    deliveryLabels[loan.deliveryType] ??
                                    loan.deliveryType
                                }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-xs px-2 py-1 rounded-full font-medium"
                                    :class="
                                        statusColors[loan.status] ??
                                        'bg-gray-100 text-gray-600'
                                    "
                                >
                                    {{
                                        statusLabels[loan.status] ?? loan.status
                                    }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <button
                                    @click="openModal(loan)"
                                    class="text-xs text-emerald-700 hover:underline"
                                >
                                    Detalji
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>

            <Pagination :data="loans" />

            <!-- Modal -->
            <div
                v-if="modal"
                class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
            >
                <div
                    class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-lg flex flex-col gap-6"
                >
                    <h3 class="text-lg font-semibold text-gray-800">
                        Detalji pozajmice
                    </h3>

                    <div
                        class="bg-gray-50 rounded-xl p-4 flex flex-col gap-2 text-sm"
                    >
                        <p>
                            <span class="font-medium text-gray-700"
                                >Korisnik:</span
                            >
                            {{ selectedLoan?.user?.name }}
                            {{ selectedLoan?.user?.surname }}
                        </p>
                        <p>
                            <span class="font-medium text-gray-700"
                                >Knjiga:</span
                            >
                            {{ selectedLoan?.book?.title }}
                        </p>
                        <p>
                            <span class="font-medium text-gray-700"
                                >Datum pozajmice:</span
                            >
                            {{ selectedLoan?.loanDate }}
                        </p>
                        <p>
                            <span class="font-medium text-gray-700"
                                >Rok vraćanja:</span
                            >
                            {{ selectedLoan?.endReturnDate }}
                        </p>
                        <p v-if="selectedLoan?.returnDate">
                            <span class="font-medium text-gray-700"
                                >Datum vraćanja:</span
                            >
                            {{ selectedLoan?.returnDate }}
                        </p>
                        <p>
                            <span class="font-medium text-gray-700"
                                >Tip dostave:</span
                            >
                            {{ deliveryLabels[selectedLoan?.deliveryType] }}
                        </p>
                        <p v-if="selectedLoan?.returnType">
                            <span class="font-medium text-gray-700"
                                >Tip vraćanja:</span
                            >
                            {{ deliveryLabels[selectedLoan?.returnType] }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <p class="text-sm font-medium text-gray-700">
                            Trenutni status:
                        </p>
                        <span
                            class="text-xs px-2 py-1 rounded-full font-medium"
                            :class="
                                statusColors[selectedLoan?.status] ??
                                'bg-gray-100 text-gray-600'
                            "
                        >
                            {{ statusLabels[selectedLoan?.status] }}
                        </span>
                    </div>

                    <!-- Promena statusa samo za dozvoljene -->
                    <div v-if="canChangeStatus" class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-700"
                            >Promeni status:</label
                        >
                        <select
                            v-model="statusForm.status"
                            class="border border-gray-200 rounded-lg px-4 py-2 text-sm outline-none"
                        >
                            <option
                                v-for="(label, value) in allowedStatuses"
                                :key="value"
                                :value="value"
                            >
                                {{ label }}
                            </option>
                        </select>
                    </div>

                    <div class="flex gap-3">
                        <button
                            @click="modal = false"
                            class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg text-sm hover:bg-gray-50"
                        >
                            Otkaži
                        </button>
                        <button
                            v-if="canChangeStatus"
                            @click="submitStatus"
                            class="flex-1 bg-emerald-700 text-white py-2 rounded-lg text-sm hover:bg-emerald-600"
                        >
                            Sačuvaj
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Pagination from "@/Pages/Components/Pagination.vue";

defineProps({
    loans: Object,
});

const page = usePage();
const userType = page.props.auth.user?.userType;

const modal = ref(false);
const selectedLoan = ref(null);

const statusForm = useForm({
    status: "",
});

const statusLabels = {
    manual_pickup_requested: "Zahtev za ručno preuzimanje",
    manual_picked_up: "Ručno preuzeta",
    sending_by_post: "Slanje poštom",
    post_arrived_for_pickup: "Pošta stigla za uručenje",
    postal_pickup: "Poštansko preuzimanje",
    postal_pickup_cancelled: "Otkazano poštansko preuzimanje",
    returned_unwanted_by_post: "Nepoželjno vraćeno poštom",
    return_on_time_started: "Početak vraćanja na vreme",
    return_late_started: "Početak vraćanja sa zakašnjenjem",
    manual_return: "Ručno vraćanje",
    postal_return: "Vraćanje poštom",
    return_sent_by_post: "Vraćanje poslato poštom",
    return_arrived_by_post: "Vraćanje pristiglo iz pošte",
    returned_on_time: "Vraćena na vreme",
    returned_late: "Vraćena sa zakašnjenjem",
};

const statusColors = {
    manual_pickup_requested: "bg-yellow-100 text-yellow-700",
    manual_picked_up: "bg-blue-100 text-blue-700",
    sending_by_post: "bg-blue-100 text-blue-700",
    post_arrived_for_pickup: "bg-purple-100 text-purple-700",
    postal_pickup: "bg-purple-100 text-purple-700",
    postal_pickup_cancelled: "bg-red-100 text-red-600",
    returned_unwanted_by_post: "bg-red-100 text-red-600",
    return_on_time_started: "bg-orange-100 text-orange-700",
    return_late_started: "bg-red-100 text-red-600",
    manual_return: "bg-orange-100 text-orange-700",
    postal_return: "bg-orange-100 text-orange-700",
    return_sent_by_post: "bg-orange-100 text-orange-700",
    return_arrived_by_post: "bg-orange-100 text-orange-700",
    returned_on_time: "bg-emerald-100 text-emerald-700",
    returned_late: "bg-gray-100 text-gray-600",
};

const deliveryLabels = {
    library: "Preuzimanje u biblioteci",
    physical: "Fizička dostava",
};

// Bibliotekar menja statuse vezane za biblioteku
const librarianStatuses = {
    manual_pickup_requested: "Zahtev za ručno preuzimanje",
    manual_picked_up: "Ručno preuzeta",
    sending_by_post: "Slanje poštom",
    postal_pickup_cancelled: "Otkazano poštansko preuzimanje",
    return_on_time_started: "Početak vraćanja na vreme",
    return_late_started: "Početak vraćanja sa zakašnjenjem",
    manual_return: "Ručno vraćanje",
    returned_on_time: "Vraćena na vreme",
    returned_late: "Vraćena sa zakašnjenjem",
};

// Poštar menja statuse vezane za poštu
const postmanStatuses = {
    sending_by_post: "Slanje poštom",
    post_arrived_for_pickup: "Pošta stigla za uručenje",
    postal_pickup: "Poštansko preuzimanje",
    returned_unwanted_by_post: "Nepoželjno vraćeno poštom",
    postal_return: "Vraćanje poštom",
    return_sent_by_post: "Vraćanje poslato poštom",
    return_arrived_by_post: "Vraćanje pristiglo iz pošte",
};

const allowedStatuses = computed(() => {
    if (userType === "admin") return statusLabels;
    if (userType === "librarian") return librarianStatuses;
    if (userType === "postman") return postmanStatuses;
    return {};
});

const canChangeStatus = computed(() => {
    return ["admin", "librarian", "postman"].includes(userType);
});

const openModal = (loan) => {
    selectedLoan.value = loan;
    statusForm.status = loan.status;
    modal.value = true;
};

const submitStatus = () => {
    statusForm.patch(route("loans.status", selectedLoan.value.loanId), {
        onSuccess: () => (modal.value = false),
    });
};
</script>
