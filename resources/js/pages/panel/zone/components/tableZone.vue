<template>
    <div class="container-table">
        <LoadingTable v-if="loading" :headers="7" :row-count="12" />
        <div v-else class="table-content">
            <div class="table-container">
                <div class="table-responsive">
                    <Table>
                        <TableHeader class="table-header-row">
                            <TableRow>
                                <TableHead class="table-head-id">ID</TableHead>
                                <TableHead class="table-head">Nombre</TableHead>
                                <TableHead class="table-head">Fecha de creación</TableHead>
                                <TableHead class="table-head">Fecha de modificación</TableHead>
                                <TableHead class="table-head-status">Estado</TableHead>
                                <TableHead class="table-head-actions">Acciones</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody class="table-body">
                            <TableRow v-for="zone in zoneList" :key="zone.id" class="table-row">
                                <td class="cell-id">{{ zone.id }}</td>
                                <td class="cell-data">{{ zone.name }}</td>
                                <td class="cell-data">{{ zone.created_at }}</td>
                                <td class="cell-data">{{ zone.updated_at }}</td>
                                <td class="cell-status">
                                    <span v-if="zone.status === true" class="status-badge status-active">
  <span class="status-indicator status-indicator-active"></span>
  Activo
</span>
<span v-else class="status-badge status-inactive">
  <span class="status-indicator status-indicator-inactive"></span>
  Inactivo
</span>

                                </td>
                                <td class="cell-actions">
                                    <div class="actions-container">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="action-button"
                                            @click="openModal(zone.id)"
                                            title="Editar proveedor"
                                        >
                                            <UserPen class="action-icon" />
                                            <span class="sr-only">Editar proveedor</span>
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="action-button-2"
                                            @click="openModalDelete(zone.id)"
                                            title="Eliminar proveedor"
                                        >
                                            <Trash class="action-icon" />
                                            <span class="sr-only">Eliminar proveedor</span>
                                        </Button>
                                    </div>
                                </td>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>
            <div class="pagination-container">
                <div class="pagination-summary">
                    Mostrando <span class="pagination-emphasis">{{ zonePaginate.from || 0 }}</span> a
                    <span class="pagination-emphasis">{{ zonePaginate.to || 0 }}</span> de
                    <span class="pagination-emphasis">{{ zonePaginate.total }}</span> proveedores
                </div>
                <PaginationZone :meta="zonePaginate" @page-change="$emit('page-change', $event)" />
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import LoadingTable from '@/components/loadingTable.vue';
import PaginationZone from '@/components/pagination.vue';
import Button from '@/components/ui/button/Button.vue';
import { Table, TableBody, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useToast } from '@/components/ui/toast';
import { Pagination } from '@/interface/paginacion';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { Trash, UserPen } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { ZoneResource } from '../interface/Zone';

const { toast } = useToast();

const emit = defineEmits<{
    (e: 'page-change', page: number): void;
    (e: 'open-modal', id_zone: number): void;
    (e: 'open-modal-delete', id_zone: number): void;
}>();
const page = usePage<SharedData>();

const message = ref(page.props.flash?.message || '');

onMounted(() => {
    if (message.value) {
        toast({
            title: 'Notificación',
            description: message.value,
        });
    }
});

const { zoneList, zonePaginate } = defineProps<{
    zoneList: ZoneResource[];
    zonePaginate: Pagination;
    loading: boolean;
}>();

const openModal = (id: number) => {
    emit('open-modal', id);
};

const openModalDelete = (id: number) => {
    emit('open-modal-delete', id);
};
</script>
<style scoped lang="css"></style>
