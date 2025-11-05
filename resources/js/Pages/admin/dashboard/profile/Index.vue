<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { defineProps, computed } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";

const props = defineProps({
    president: {
        type: Object,
        required: true,
        default: () => ({
            id: null,
            name: "",
            email: "",
            position: "",
            term_start: null,
            term_end: null,
            status: 1,
        }),
    },
});

const formattedDate = (dateString) => {
    if (!dateString) return "Not available";
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const isActive = computed(() => props.president.status === 1);
</script>

<template>
    <div>
        <Head title="President Profile" />
        <AdminLayout>
            <div class="container my-5">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card shadow-sm rounded">
                            <div class="card-header bg-dark text-white">
                                <h3 class="mb-0">President Profile</h3>
                            </div>
                            <div class="card-body">
                                <!-- President Info -->
                                <div class="d-flex align-items-center mb-4">
                                    <div
                                        class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center"
                                        style="width: 80px; height: 80px; font-size: 2rem;"
                                    >
                                        {{ props.president.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div class="ms-4">
                                        <h4 class="mb-0">{{ props.president.name }}</h4>
                                        <p class="text-muted mb-0">{{ props.president.email }}</p>
                                        <small :class="isActive ? 'text-success' : 'text-danger'">
                                            {{ isActive ? "Active" : "Inactive" }}
                                        </small>
                                    </div>
                                </div>

                                <!-- Details -->
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <strong>Position:</strong> {{ props.president.position }}
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Term Start:</strong> {{ formattedDate(props.president.term_start) }}
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Term End:</strong> {{ formattedDate(props.president.term_end) }}
                                    </li>
                                </ul>

                                <!-- Actions -->
                                <div class="mt-4 d-flex justify-content-end">
                                    <Link
                                        :href="route('role.official.edit', {official: props?.president.id})"
                                        class="btn btn-primary me-2"
                                    >
                                        Edit Profile
                                    </Link>
                                    <Link
                                        :href="route('dashboard')"
                                        class="btn btn-secondary"
                                    >
                                        Back to Dashboard
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    </div>
</template>

<style scoped>
.card-header {
    border-bottom: 0;
}
.rounded-circle {
    font-weight: bold;
    background-color: #6c757d !important;
}
.list-group-item {
    font-size: 1rem;
}
</style>
