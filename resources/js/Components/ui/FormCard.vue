<template>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between" v-if="$slots.cardHeaderAction || props.cardTitle">
            <h3 class="mb-0 txt_16 fw-medium" v-if="props.cardTitle">{{cardTitle}}</h3>
            <div class="ms-auto flex-grow-1" v-if="$slots.cardHeaderAction">
                <div class="container-fluid px-0">
                    <div class="row gx-1">
                        <slot name="cardHeaderAction"/>
                    </div>
                </div>
            </div>
        </div>
        <form @submit.prevent="submitHandlerFnCallback">
            <div class="card-body" :class="{'p-0': removeBodyPadding}">
                <slot/>
            </div>
            <div class="card-footer text-secondary" v-if="$slots.cardFooter">
                <slot name="cardFooter"/>
            </div>
        </form>
    </div>
</template>

<script setup>
    const props = defineProps({
        cardTitle:{
            type:String,
            required:false,
        },
        removeBodyPadding:{
            type:Boolean,
            default:false,
        },
        submitHandlerFnCallback:{
            type:Function,
            required:true,
        }
    })
</script>
