<template>
    <Card :card-title="trans('settings.projects.form.charac_title')">
        <div class="row">
            <div class="col-lg-9">
                <FormField class="form-floating">
                    <TextInput v-model.trim="form.name"
                               type="text"
                               :placeholder="trans('settings.projects.form.name')"
                               id="name"
                               maxlength="255"
                               class="form-control-lg"
                               :class="{'is-invalid':form.errors.name}"
                               @focusout="focus_out_project_name"
                               autofocus
                               required
                    />
                    <InputLabel for="name" :value="trans('settings.projects.form.name')" required/>
                    <InputError :message="form.errors.name"/>
                    <ProjectPermalien v-if="project" class="mt-2" :form="form"/>
                    <ProjectPermalienCreation ref="permalienCreationRef" v-else class="mt-2" :form="form"/>
                </FormField>

                <FormField class="form-floating" no-margin-bottom>
                    <TextInput v-model.trim="form.short_desc"
                               type="text"
                               maxlength="255"
                               :placeholder="trans('settings.projects.form.desc')"
                               id="name"
                               :class="{'is-invalid':form.errors.short_desc}"
                               required
                    />
                    <InputLabel for="name" :value="trans('settings.projects.form.desc')" required/>
                    <InputError :message="form.errors.name"/>
                </FormField>
            </div>
            <div class="col-lg-3">
                <ProjectPhoto :form="form"/>
            </div>
        </div>

    </Card>
</template>

<script setup>

import Card from "@/Components/ui/Card.vue";
import TextInput from "@/Components/ui/form/TextInput.vue";
import InputError from "@/Components/ui/form/InputError.vue";
import FormField from "@/Components/ui/form/FormField.vue";
import InputLabel from "@/Components/ui/form/InputLabel.vue";
import ProjectPermalien from "@/Pages/Settings/Projects/partials/form/ProjectPermalien.vue";
import ProjectPhoto from "@/Pages/Settings/Projects/partials/form/ProjectPhoto.vue";
import {computed, ref} from "vue";
import {usePage} from "@inertiajs/vue3";
import ProjectPermalienCreation from "@/Pages/Settings/Projects/partials/form/ProjectPermalienCreation.vue";
import {trans} from "@/Helpers/translations.js";

const project = computed(() => usePage().props.project ?? null)
const props = defineProps({
    form: {
        type: Object,
        required: true
    }
})
const permalienCreationRef = ref(null);
const focus_out_project_name = () =>{
    if(permalienCreationRef.value){
        permalienCreationRef.value.generate_first_permalink()
    }
}
</script>
