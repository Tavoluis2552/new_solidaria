<template>
    <Head title="Nueva "></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card class="mt-4 flex flex-col gap-4">
                <CardHeader>
                    <CardTitle>Editar Rol</CardTitle>
                    <CardDescription>Modifique los campos para editar el rol</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit="onSubmit" class="flex flex-col gap-6">
                        <FormField v-slot="{ componentField }" name="name">
                            <FormItem>
                                <FormLabel>Nombre</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="Nombre" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="permisos">
                            <FormItem>
                                <FormLabel></FormLabel>
                                <FormControl>
                                    <div class="flex gap-6">
                                        <!-- Permisos Disponibles -->
                                        <div class="flex flex-[5] flex-col">
                                            <span class="mb-2 text-sm font-medium text-muted-foreground">Permisos Disponibles:</span>
                                            <div class="flex h-64 flex-col gap-1 overflow-y-auto rounded-xl border bg-muted p-3 shadow-sm">
                                                <div
                                                    v-for="permiso in availablePermissions"
                                                    :key="permiso.id"
                                                    class="cursor-pointer rounded-lg px-3 py-2 text-sm transition-colors hover:bg-green-400 hover:text-white"
                                                    @click="moveToAssigned(permiso)"
                                                >
                                                    {{ permiso.name }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Botones -->
                                        <div class="flex flex-[1] items-center justify-center">
                                            <div class="flex flex-col gap-4">
                                                <Button
                                                    type="button"
                                                    size="icon"
                                                    variant="secondary"
                                                    class="rounded-full shadow"
                                                    @click="availablePermissions.forEach((p) => moveToAssigned(p))"
                                                >
                                                    <ChevronsRight class="h-5 w-5" />
                                                </Button>
                                                <Button
                                                    type="button"
                                                    size="icon"
                                                    variant="secondary"
                                                    class="rounded-full shadow"
                                                    @click="assignedPermissions.forEach((p) => moveToAvailable(p))"
                                                >
                                                    <ChevronsLeft class="h-5 w-5" />
                                                </Button>
                                            </div>
                                        </div>

                                        <!-- Permisos Asignados -->
                                        <div class="flex flex-[5] flex-col">
                                            <span class="mb-2 text-sm font-medium text-muted-foreground">Permisos Asignados:</span>
                                            <div class="flex h-64 flex-col gap-1 overflow-y-auto rounded-xl border bg-muted p-3 shadow-sm">
                                                <div
                                                    v-for="permiso in assignedPermissions"
                                                    :key="permiso.id"
                                                    class="cursor-pointer rounded-lg px-3 py-2 text-sm transition-colors hover:bg-green-400 hover:text-white"
                                                    @click="moveToAvailable(permiso)"
                                                >
                                                    {{ permiso.name }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </FormControl>

                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <div class="container m-3 flex justify-end gap-4">
                            <Button type="submit" variant="default"> Actualizar </Button>
                            <Button type="button" variant="destructive" @click="cancelEdit"> Cancelar </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import { onMounted, ref, watch } from 'vue';
import * as z from 'zod';
// composable
import { useRole } from '@/composables/useRole';
import { showSuccessMessage } from '@/utils/message';
import { ChevronsLeft, ChevronsRight } from 'lucide-vue-next';

// Definir props primero
const props = defineProps<{
    permisos: { id: number; name: string }[]; // Todos los permisos disponibles
    roleData: { id: number; name: string; permisos: { id: number; name: string }[] }; // Información del rol
}>();

const { updateRole } = useRole();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: '/panel/roles',
    },
    {
        title: 'Editar Rol',
        href: '/panel/roles/edit',
    },
];

// Form validation
const formSchema = toTypedSchema(
    z.object({
        name: z
            .string({ message: 'Campo obligatorio' })
            .min(1, { message: 'Nombre mayor a 5 letras' })
            .max(50, { message: 'Nombre menor a 50 letras' }),
    }),
);

// Form submit
const { handleSubmit } = useForm({
    validationSchema: formSchema,
    initialValues: {
        name: props.roleData.name,
    },
});

const availablePermissions = ref<{ id: number; name: string }[]>([]);
const assignedPermissions = ref<{ id: number; name: string }[]>([]);

// Enviar formulario
const onSubmit = handleSubmit(async (values) => {
    await updateRole(props.roleData.id, {
        name: values.name,
        permisos: assignedPermissions.value.map((p) => p.id),
    });

    router.visit(route('panel.roles.index'), {
        method: 'get',
        data: {},
        preserveScroll: true,
        onSuccess: () => {
            showSuccessMessage('Rol actualizado', 'El rol se actualizó correctamente');
        },
    });

    console.log('Rol actualizado:', values.name, assignedPermissions.value);
});

// Cargar datos cuando llegan
watch(
    () => props.roleData,
    (newVal) => {
        if (newVal) {
            assignedPermissions.value = newVal.permisos;
            availablePermissions.value = props.permisos.filter((p) => !newVal.permisos.some((ap) => ap.id === p.id));
        }
    },
    { immediate: true, deep: true },
);

// Inicializar valores y permisos al montar el componente
onMounted(() => {
    assignedPermissions.value = props.roleData.permisos;
    availablePermissions.value = props.permisos.filter((p) => !assignedPermissions.value.some((ap) => ap.id === p.id));
});

// Cancelar edición y redirigir al index
const cancelEdit = () => {
    router.visit(route('panel.roles.index'));
};

const moveToAssigned = (permiso: { id: number; name: string }) => {
    assignedPermissions.value.push(permiso);
    availablePermissions.value = availablePermissions.value.filter((p) => p.id !== permiso.id);
};

const moveToAvailable = (permiso: { id: number; name: string }) => {
    availablePermissions.value.push(permiso);
    assignedPermissions.value = assignedPermissions.value.filter((p) => p.id !== permiso.id);
};

console.log(props.permisos);
</script>

<style scoped></style>
