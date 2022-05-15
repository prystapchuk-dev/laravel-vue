<template>
  <PageComponent>
    <template v-slot:header>
      <div class="flex items-center justify-between">
        <h1 class="text-3x1 font-bold text-gray-900">
          {{ route.params.id ? model.title : 'Create a Survey'}}
        </h1>
        <button v-if="route.params.id"
                @click="deleteSurvey()"
                type="button"
                class="py-2 px-3 text-white bg-red-500 rounded-md hover:bg-red-700">
          <TrashIcon class="w-6 h-6 inline-block"/>
          Видалити опитування
        </button>
      </div>
    </template>
    <div v-if="surveyLoading" class="flex justify-center">
      <RefreshIcon class="animate-spin w-6 h-6"/>
      Завантаження...
    </div>
    <form v-else
          @submit.prevent="saveSurvey"
          class="animate-fade-in-down">
      <div class="shadow sm:rounded-md sm:overflow-hidden">
        <!-- Survey Fields -->
        <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
          <!-- Image -->
          <div>
            <label class="block text-sm font-medium text-gray-700">
              Зображення
            </label>
            <div class="mt-1 flex items-center">
              <img
                v-if="model.image_url"
                :src="model.image_url"
                :alt="model.title"
                class="w-64 h-48 object-cover"
              />
              <span
                v-else
                class="
                  flex
                  items-center
                  justify-center
                  h-12
                  w-12
                  rounded-full
                  overflow-hidden
                  bg-gray-100"
              >
                <PhotographIcon class="w-[80%] h-[80%] text-gray-300"/>
              </span>
              <button
                type="button"
                class="
                  relative
                  overflow-hidden
                  ml-5
                  bg-white
                  py-2
                  px-3 border border-gray-300
                  rounded-md
                  shadow-sm
                  text-sm
                  leading-4
                  font-medium
                  text-gray-700
                  hover:bg-gray-50
                  focus:ring-2
                  focus:ring-offset-2
                  focus:ring-indigo-500"
              >
                <input
                  type="file"
                  @change="onImageChoose"
                  class="
                    absolute l
                    eft-0
                    top-0
                    right-0
                    bottom-0
                    opacity-0
                    cursor-pointer"
                />
              Змінити
              </button>
            </div>
          </div>
          <!--/ Image -->

          <!-- Title -->
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700">
              Заголовок
            </label>
            <input
              type="text"
              name="title"
              id="title"
              v-model="model.title"
              autocomplete="survey_title"
              class="
                mt-1
                focus:ring-indigo-500 focus:border-indigo-500
                block
                w-full
                shadow-sm
                sm:text-sm
                border-gray-300
                rounded-md"
            >
          </div>
          <!--/ Title -->

          <!-- Description -->
          <div>
            <label for="about" class="block text-sm font-medium text-gray-700">
              Опис
            </label>
            <div class="mt-1">
              <textarea v-model="model.description" id="description" name="description" rows="3" autocomplete="survey_description" placeholder="Опишіть своє опитування" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md">
              </textarea>
            </div>
          </div>
          <!--/ Description -->

          <!-- Expire Date -->
          <div>
            <label for="expire_date" class="block text-sm font-medium text-gray-700">
              Заголовок
            </label>
            <input v-model="model.expire_date" type="date" name="expire_date" id="expire_date" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"/>
          </div>
          <!--/ Expire Date -->

          <!-- Status -->
          <div class="flex items-start">
            <div class="flex items-center h-5">
              <input id="status" name="status" type="checkbox" v-model="model.status" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
            </div>
            <div class="ml-3 text-sm">
              <label for="expire_date" class="block text-sm font-medium text-gray-700">
               Активний
              </label>
            </div>
          </div>
          <!--/ Status -->
        </div>
        <!--/ Survey Fields -->

        <div class="px-4 py-6 bg-white space-y-6 sm:p-6">
          <h3 class="text-2xl font-semibold flex items-center justify-between">
            Запитання

            <!-- Add new question -->
            <button @click="addQuestion()" type="button" class="flex items-center text-sm py-1 px-4 rounded-sm text-white bg-gray-600 hover:bg-gray-700">
              <PlusIcon class="w-4 h-4 mr-0.5"/>
              Додати запитання
            </button>
            <!--/ Add new question -->
          </h3>

          <div v-for="(question, index) in model.questions" :key="question.id">
            <QuestionEditor
              @change="questionChange"
              @addQuestion="addQuestion"
              @deleteQuestion="deleteQuestion"
              :question="question"
              :index="index"/>
          </div>
        </div>

        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
          <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Зберегти
          </button>
        </div>
      </div>
    </form>
  </PageComponent>
</template>

<script setup>
import PageComponent from "../components/PageComponent.vue";
import QuestionEditor from "../components/editor/QuestionEditor.vue";
import {computed, ref, watch} from "vue";
import store from "../store";
import { v4 as uuidv4 } from "uuid";
import { useRoute, useRouter } from "vue-router";
import { PhotographIcon, PlusIcon, RefreshIcon, TrashIcon } from "@heroicons/vue/solid";

const router = useRouter();
const route = useRoute();

let model = ref({
  title: '',
  status: false,
  description: null,
  image_url: null,
  expire_date: null,
  questions: [],
});

const surveyLoading = computed(() => store.state.currentSurvey.loading);

watch(
  () => store.state.currentSurvey.data,
  (newVal, oldVal) => {
    model.value = {
      ...JSON.parse(JSON.stringify(newVal)),
      status: newVal.status !== "draft",
    };
  }
);

if (route.params.id) {
  store.dispatch('getSurvey', route.params.id);
}

function addQuestion(index) {
  const newQuestion = {
    id: uuidv4(),
    type: '',
    question: '',
    description: null,
    data: {},
  };

  model.value.questions.splice(index, 0, newQuestion);
}

function onImageChoose(ev) {
const file = ev.target.files[0];

const reader = new FileReader();
reader.onload = () => {
  model.value.image = reader.result;
  model.value.image_url = reader.result;
};
reader.readAsDataURL(file);

}

function deleteQuestion(question) {
  model.value.questions = model.value.questions.filter((qst) => qst !== question);
}

function questionChange(question) {
  if (question.data.options) {
    question.data.options = [...question.data.options];
  }

  model.value.questions = model.value.questions.map((q) => {
    if (q.id === question.id) {
      return JSON.parse(JSON.stringify(question));
    }
    return  q;
  });
}

function saveSurvey() {
  console.log(model.value);
  store.dispatch('saveSurvey', model.value)
    .then(({ data }) => {
        store.commit('notify', {
          type: 'success',
          message: 'Опитування успішно збережено',
        })
        router.push({
           name: 'SurveyView',
           params: { id: data.data.id},
     });
  });
}

function deleteSurvey() {
  if (confirm('Ви дійсно бажаєте видалити опитування')) {
    store.dispatch('deleteSurvey', model.value.id)
      .then(() => {
        router.push({
          name: 'Surveys',
        });
      });
  }
}
</script>

<style scoped>

</style>
