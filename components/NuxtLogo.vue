<template>
  <div class="col-span-1 pr-32">
    <div class="relative">
      <h1 class="text-white text-lg uppercase"># From JESUS</h1>
      <p class="font-bold text-white uppercase text-5xl mb-8">to the world.</p>
      <div class="h-32">
        <transition name="home">
          <div v-if="step === 1" class="relative flex items-center">
            <input
              v-model="search"
              class="w-full h-full rounded-full p-4 focus:outline-none z-10 shadow-md"
              :placeholder="`Search in ${username}'s repositories...`"
              @keydown="searchRepositories"
            />
            <button
              class="absolute right-1 rounded-full bg-yellow-400 w-28 text-white uppercase font-bold z-20"
              :class="{ 'opacity-50 pointer-events-forbbiden': !isValid }"
              style="height: 85%"
              @click="chooseRepo"
            >
              Next
            </button>
            <div
              v-if="repository.length > 0"
              class="absolute w-full bg-white pt-10 rounded-bl-xl rounded-br-xl max-h-60 overflow-y-auto shadow-2xl"
              style="top: calc(100% - 24px)"
            >
              <p
                v-for="(repo, i) in repository"
                :key="i"
                class="px-4 py-2 cursor-pointer"
                :class="{
                  'border-b border-gray-200': i !== repository.length - 1,
                }"
                @click="selectRepository(i)"
              >
                {{ repo.name }}
              </p>
            </div>
          </div>
        </transition>
        <transition name="home">
          <div v-if="step === 2" class="relative flex items-center">
            <input
              v-model="domain"
              class="w-full h-full rounded-full p-4 focus:outline-none z-10 shadow-md"
              placeholder="Choose subdomain name..."
              @keydown="searchRepositories"
            />
            <button
              class="absolute right-1 rounded-full bg-yellow-400 w-28 text-white uppercase font-bold z-20"
              :class="{ 'opacity-50 pointer-events-none': !domain }"
              style="height: 85%"
              @click="checkDomain"
            >
              Check
            </button>
          </div>
        </transition>
        <transition name="home">
          <div v-if="step === 3" class="relative flex items-center gap-8">
            <!-- Foreach frameworks with buttons -->
            <button
              v-for="(framework, i) in frameworks"
              :key="i"
              class="flex w-20 h-20 rounded-full p-4 focus:outline-none z-10 shadow-md bg-white"
              @click="selectFramework(framework.name)"
            >
              <img :src="framework.logo" alt="framework logo" />
            </button>
          </div>
        </transition>
      </div>
    </div>
  </div>
</template>

<style>
.nuxt-logo {
  height: 180px;
}
</style>
