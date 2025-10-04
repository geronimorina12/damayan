<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
</script>

<template>
    <Head title="Settings" />
    <div>
        <AdminLayout>
            <div class="container text-start fs-4">Settings</div>
            <hr>
        <div class="container-fluid d-flex flex-column">
            <div class="list">
                <Link :href="route('role.index')" class="link text-decoration-none fs-5">User Role and Management</Link>
            </div>
            <hr>
            <div class="list">
                <Link :href="route('activityLog.index')" class="link text-decoration-none fs-5">Activity Logs</Link>
            </div>
            <hr>
            <div class="list">
                <Link :href="route('smsNotification.index')" class="link text-decoration-none fs-5">SMS Notification</Link>
            </div>
            <hr>
        </div>
        </AdminLayout>
    </div>
</template>

<style scoped>
.container-fluid .list{
    position: relative;
    width: 100%;
    height: 30px;
}
.list .link{
    position: absolute;
    width: 100%;
    color: #333;
}
.list .link:hover{
    color: #0d6efd;
    text-decoration: underline;
}
</style>