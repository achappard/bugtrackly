<template>
    <Card card-title="Liste des projets" :remove-body-padding="true">
        <template #cardHeaderAction>
            <InputLabel for="search_user"
                        class="ms-auto col-sm-6 col-lg-8 col-xl-10 col-form-label col-form-label-sm text-end">
                Rechercher un projet :
            </InputLabel>
            <div class="col-sm-6 col-lg-4 col-xl-2">
                <TextInput type="search" id="search_user" v-model="params.search" placeholder="Nom du projet" class="form-control-sm" autofocus/>
            </div>
        </template>
        <template #cardFooter>
            <Pagination :items="items" item-singular-name="projet" item-plural-name="projets" />
        </template>
        <table class="table table-bordered table-hover mb-0 caption-top" v-if="items.data.length">
            <thead>
            <tr>
                <th :class="sortingClass('name', params)" @click="sort('name')">Nom</th>
                <th>Utilisateurs</th>
                <th class="date-col" :class="sortingClass('date', params)" @click="sort('date')">Date</th>
            </tr>
            </thead>
            <tbody>
                <tr v-for="item in items.data" :key="item.id">
                    <td class="fw-medium">
                        <div class="d-flex align-items-center">
                            <Link class="fw-bold" :href="route('settings.projects.show', item.id)">
                                {{item.name}}
                            </Link>
                        </div>
                        <div class="row-actions">
                            <Link :href="route('settings.projects.show', item.id)">Modifier</Link>
                            <span class="mx-1 text-gray">|</span>
                            <button class="btn btn-sm btn-sm border-0 p-0 btn-link text-danger"
                                    @click="store.commit('projectsManagement/setProjectToDelete', item)"
                                    type="button">
                                Supprimer
                            </button>
                        </div>
                    </td>
                    <td>
                        <AvatarsList :items="item.users"/>
<!--                        <ul class="list-inline mb-0">
                            <li v-for="user in item.users"
                                :key="user.id"
                                class="list-inline-item">
                                <ButtonUserAvatar :href="route('settings.users.show', user.id)" :user="user" :is-link="true"/>
                            </li>
                        </ul>-->
                    </td>
                    <td class="text-sm text-secondary">
                        <InfoProject :project="item"/>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="p-5" v-else>
            <p class="mb-0 text-center">{{ no_result }}</p>
        </div>
    </Card>
</template>

<script setup>

import Card from "@/Components/ui/Card.vue";
import TextInput from "@/Components/ui/form/TextInput.vue";
import InputLabel from "@/Components/ui/form/InputLabel.vue";
import {computed, ref, watch} from "vue";
import {Link, router, usePage} from "@inertiajs/vue3";
import {pickBy, throttle} from "lodash";
import Pagination from "@/Components/ui/Pagination.vue";
import {sortingClass} from "@/Helpers/datatable.js";
import Avatar from "@/Components/ui/user/avatar.vue";
import InfoProject from "@/Components/ui/project/InfoProject.vue";
import {useStore} from "vuex";
import ButtonUserAvatar from "@/Components/ui/form/ButtonUserAvatar.vue";
import AvatarsList from "@/Pages/Settings/Projects/partials/AvatarsList.vue";
const store = useStore();
const items = computed(()=>usePage().props.projects);

/**
 * Filter received from the controller
 * @type {ComputedRef<unknown>}
 */
const filters = computed(() => usePage().props.filters);

/**
 * Params send to the controller
 * @type {Ref<UnwrapRef<{search, field: *, direction}>>}
 */
const params = ref({
    search: filters.value.search,
    field: filters.value.field,
    direction: filters.value.direction
});

const no_result = computed( () => filters.value.search !== null ? "Aucun projet trouvé" : "Aucun projet enregistré")

/**
 * Sort handler on columns header
 * @param field
 */
const sort = (field) => {
    params.value.field = field
    params.value.direction = params.value.direction === 'asc' ? 'desc' : 'asc';
}

/**
 * Watcher for params
 * Make an Inertia request with cleaned params
 */
watch(params, throttle(function () {
    //clean empty params
    const my_params = pickBy(params.value);

    //request
    router.get(route('settings.projects.index'), my_params, {replace: true, preserveState: true})
}, 300), {deep: true})
</script>
<style scoped>
.date-col{
    width: 250px;
}
</style>
