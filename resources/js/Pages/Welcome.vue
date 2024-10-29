<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';


const expandedRows = ref({});
const users = ref([]);
const totalRecords = ref(0);
const currentPage = ref(0);
const rowsPerPage = ref(5);

const filter = ref({
    name: '',
    email: ''
});

const applyFilters = () => {
    axios.get(route('filter'), {
        params: {
            ...filter.value,
            perPage: rowsPerPage.value,
            page: currentPage.value + 1
        }
    }).then(response => {
        users.value = response.data.users.data;
        totalRecords.value = response.data.users.total;
        currentPage.value = response.data.users.current_page - 1;
    }).catch(error => {
        console.error('Erro ao aplicar filtros:', error);
    });
};

const onPageChange = (event) => {
    currentPage.value = event.page;
    rowsPerPage.value = event.rows;
    applyFilters();
};

onMounted(() => {
    applyFilters();
});
</script>

<template>
    <div class="container mx-auto">
        <DataTable v-model:expandedRows="expandedRows" :value="users" dataKey="id" paginator :rows="rowsPerPage"
            :totalRecords="totalRecords" :first="currentPage * rowsPerPage"
            :rowsPerPageOptions="[5, 10, 20, 30, 50, 100, 200, 500, 1000]" @page="onPageChange"
            tableStyle="min-width: 60rem" lazy :size="'small'">
            <template #header>
                <div class="flex flex-wrap justify-end gap-2">
                    <InputText v-model="filter.name" placeholder="Nome..." />
                    <InputText v-model="filter.email" placeholder="Email..." />
                    <Button label="Filtrar" @click="applyFilters" />
                </div>
            </template>
            <Column expander style="width: 5rem" />
            <Column field="id" header="ID"></Column>
            <Column field="name" header="Nome"></Column>
            <Column field="email" header="Email"></Column>
            <template #expansion="slotProps">
                <div class="p-4">
                    <h5>Posts de {{ slotProps.data.name }}</h5>
                    <DataTable :value="slotProps.data.posts">
                        <Column field="id" header="ID" sortable></Column>
                        <Column field="title" header="Título" sortable></Column>
                        <Column field="description" header="Descrição" sortable></Column>
                    </DataTable>
                </div>
            </template>
        </DataTable>
    </div>
</template>
