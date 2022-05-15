<template>
    <PageComponent title="Панель">
      <div v-if="loading" class="flex justify-center">Завантаження...</div>
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 text-gray-700">
        <div class="animate-fade-in-down bg-white shadow-md p-3 text-center flex flex-col order-1 lg:order-2" style="animation-delay: 0.1s">
          <h3 class="text-2xl font-semibold">Усього опитувань</h3>
          <div class="text-8xl font-semibold flex-1 flex items-center justify-center">
            {{ data.totalSurveys }}
          </div>
        </div>
        <div class="animate-fade-in-down bg-white shadow-md p-3 text-center flex flex-col order-2 lg:order-4" style="animation-delay: 0.2s">
          <h3 class="text-2xl font-semibold">Усіх відповідей</h3>
          <div class="text-8xl font-semibold flex-1 flex items-center justify-center">
            {{ data.totalAnswers }}
          </div>
        </div>
        <div class="animate-fade-in-down row-span-2 bg-white shadow-md p-4 order-3 lg:order-1" style="animation-delay: 0.3s">
          <h3 class="text-2xl font-semibold">Останнє опитування</h3>
          <img :src="data.latestSurveys.image_url" class="w-[240px] mx-auto" :alt="data.latestSurveys.title">
          <h3 class="font-bold text-xl mb-3">{{ data.latestSurveys.title }}</h3>
          <div class="flex justify-between text-sm mb-1">
            <div>Дата створення</div>
            <div>{{ data.latestSurveys.created_at }}</div>
          </div>
          <div class="flex justify-between text-sm mb-1">
            <div>Дата закінчення</div>
            <div>{{ data.latestSurveys.expire_date }}</div>
          </div>
          <div class="flex justify-between text-sm mb-1">
            <div>Статус</div>
            <div>{{ data.latestSurveys.status ? 'Активний' : 'В розробці'}}</div>
          </div>
          <div class="flex justify-between text-sm mb-3">
            <div>Запитань:</div>
            <div>{{ data.latestSurveys.questions }}</div>
          </div>
          <div class="flex justify-between text-sm mb-3">
            <div>Відповідей:</div>
            <div>{{ data.latestSurveys.answers }}</div>
          </div>
          <div class="flex justify-between">
            <router-link
                  :to="{ name: 'SurveyView', params: { id: data.latestSurveys.id } }"
                  class="flex py-2 px-4 border border-transparent text-sm text-sm rounded-md text-indigo-500 hover:bg-indigo-700 hover:text-white transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <PencilAltIcon/>
              Редагувати
            </router-link>
            <button class="flex py-2 px-4 items-center border border-transparent text-sm rounded-md text-indigo-500 hover:bg-indigo-700 hover:text-white transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              <EyeIcon class="w-6 mr-1"/>
              Показати відповіді
            </button>
          </div>
        </div>
        <div class="animate-fade-in-down bg-white shadow-md p-3 row-span-2 order-4 lg:order-3" style="animation-delay: 0.4s">
          <div class="flex justify-between items-center mb-3 px-2">
            <h3 class="text-2xl font-semibold">Останні відповіді</h3>
            <a href="javascript:void(0)" class="text-sm text-blue-500 hover:decoration-blue-500">
              Показати всі
            </a>
          </div>
          <a href="#" v-for="answer of data.latestAnswers" :key="answer.id"
          class="block p-2 hover:bg-gray-100/90"
          >
            <div class="font-semibold">{{ answer.survey.title }}</div>
            <small>
              Відповідь створено
              <i class="font-semibold">{{ answer.end_date}}</i>
            </small>
          </a>
        </div>
      </div>
    </PageComponent>
</template>

<script setup>
import { PencilAltIcon, EyeIcon } from "@heroicons/vue/solid";
import PageComponent from "../components/PageComponent.vue";
import {computed} from "vue";
import {useStore} from "vuex";

const store = useStore();

const loading = computed(() => store.state.dashboard.loading);
const data = computed(() => store.state.dashboard.data);

store.dispatch('getDashboardData');

</script>
