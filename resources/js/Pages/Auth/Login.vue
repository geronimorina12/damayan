<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toggleLoginButton } from '@/piniaStore/ToggleLoginButtonDisable';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const useToggleButton = toggleLoginButton();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const submit = () => {
    form.post(route('login.store'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="main-container d-flex align-items-center justify-content-center">
        <div class="card p-4" style="width: 100%; max-width: 400px;">
            <div class="text-center mb-4">
                <img src="../../../images/logo1.png" alt="Logo" class="logo" />
            </div>

            <form @submit.prevent="submit" class="form p-3">
                <!-- Email -->
                <div class="mb-3">
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        class="form-control"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <InputError class="text-danger small mt-1" :message="form.errors.email" />
                </div>

                <!-- Password with toggle -->
                <div class="mb-3 position-relative">
                    <InputLabel for="password" value="Password" />
                    <div class="input-group">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            class="form-control"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        />
                        <span
                            class="input-group-text bg-white border-start-0"
                            role="button"
                            @click="togglePassword"
                        >
                            <i
                                :class="showPassword ? 'bi bi-eye' : 'bi bi-eye-slash'"
                            ></i>
                        </span>
                    </div>
                    <InputError class="text-danger small mt-1" :message="form.errors.password" />
                </div>

                <!-- Button -->
                <div class="d-grid mb-3">
                    <PrimaryButton
                        class="btn btn-dark py-2"
                        :class="{ 'disabled': form.processing || useToggleButton.attemptError == true }"
                        :disabled="form.processing"
                    >
                        Sign in
                    </PrimaryButton>
                </div>

                <!-- Forgot password -->
                <div class="text-center">
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-decoration-none text-primary text-dark"
                    >
                        Forgot password?
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
.main-container {
    width: 100vw;
    height: 100vh;
    background: #D4F3F9;
}

.card {
    border-radius: 10px;
    background: transparent;
    border: 0;
}

.logo {
    position: relative;
    width: 90%;
    height: 200px;
    margin: auto;
    object-fit: contain;
}

.form {
    background: #7FEAFE;
}
</style>
