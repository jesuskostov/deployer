<!-- Please remove this file from your project -->
<template>
  <div class="relative w-full min-h-screen sm:pt-0 custom-gradient">
    <div
      class="container mx-auto grid grid-cols-2 gap-x-12 items-center justify-center min-h-screen"
    >
      <div class="col-span-1">
        <div class="relative">
          <h1 class="animate-bounce test text-white text-4xl uppercase mb-4">
            # From JESUS <span class="relative -top-1">❤️</span>
          </h1>
          <p class="test font-bold text-white uppercase text-6xl mb-8">
            to the world.
          </p>
          <div class="w-4/5 h-32">
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
      <div class="col-span-1">
        <div @click="focus" class="console">
          <header>
            <p>
              Hello, provide us your GitHub username so we can fetch your
              repositories.
            </p>
          </header>
          <div class="consolebody">
            <div>
              > username:
              <input
                ref="username"
                :disabled="usernameSet"
                v-model="username"
                type="text"
                class="border-0 bg-transparent focus:outline-none"
                :class="{ 'pointer-events-none': usernameSet }"
                v-on:keyup.enter="onEnter"
                autofocus
              />
            </div>
            <div
              v-for="(bash, i) in bashMsg"
              :key="i"
              class="flex items-center text-sm"
            >
              >
              <p class="ml-1" v-html="bash.msg" />
            </div>
            <div v-if="step === 2" class="relative -top-2 flex items-center">
              > <span class="ml-1 text-pink-400">{{ domain }}</span
              >.g-web.agency
              <div class="ml-1 h-8">
                <img
                  v-if="domainChecking"
                  style="top: 1px"
                  class="relative w-8 h-8"
                  src="~/static/spinner.gif"
                  alt="loader"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "NuxtTutorial",
  transition: "home",
  data() {
    return {
      step: 0,
      search: "",
      repositories: [],
      repository: [],
      isValid: false,
      username: "",
      usernameSet: false,
      bashMsg: [],
      domain: "",
      domainChecking: false,
      frameworks: [
        {
          name: "vue",
          logo: "https://vuejs.org/images/logo.png",
        },
        {
          name: "nuxt",
          logo: "https://upload.wikimedia.org/wikipedia/commons/thumb/a/ae/Nuxt_logo.svg/1200px-Nuxt_logo.svg.png",
        },
        {
          name: "react",
          logo: "https://upload.wikimedia.org/wikipedia/commons/thumb/a/a7/React-icon.svg/1200px-React-icon.svg.png",
        },
        {
          name: "static",
          logo: "https://upload.wikimedia.org/wikipedia/commons/thumb/6/61/HTML5_logo_and_wordmark.svg/1200px-HTML5_logo_and_wordmark.svg.png",
        },
      ],
      framework: "",
    };
  },
  watch: {
    search(val) {
      if (val !== "") {
        // See if val match with any repository name
        const valid = this.repositories.filter((repo) =>
          repo.name.toLowerCase().includes(val.toLowerCase())
        );
        if (valid.length === 1) {
          this.isValid = true;
        }
      } else {
        this.repository = [];
      }
    },
  },
  methods: {
    selectFramework(framework) {
      this.framework = framework;
      this.bashMsg.push(
        {
          msg: `You have selected <span class="text-pink-400">${framework}</span> framework.`,
        },
        {
          msg: "Clonning the repository...",
        }
      );
      this.startBuild();
    },
    async startBuild() {
      // Installing the dependencies
      const repo = this.repository[0].ssh_url;
      const domain = this.domain;
      const framework = this.framework;
      this.bashMsg.push({
        msg: `<div class="flex items-center">
              <span>Installing dependancies...</span>
              <div class="ml-1 h-8">
                <img
                  class="relative w-8 h-8"
                  src="./spinner.gif"
                  alt="loader"
                />
              </div>
            </div>`,
      });
      await this.$axios
        .post("https://build.g-web.agency/api/install", {
          repo,
          domain,
          framework,
        })
        .then((res) => {
          // Replace the loader with the success message
          this.bashMsg[
            this.bashMsg.length - 1
          ].msg = `<div class="flex items-center">
              Dependencies installed successfully.
              <div class="ml-2 h-8 text-base relative top-1">
                 ✅ ${res.data}
              </div>
            </div>`;
          this.bashMsg.push({
            msg: `
            <div class="flex items-center">
              <span>Building the project...</span>
              <div class="ml-1 h-8">
                <img
                  style="top: 1px"
                  class="relative w-8 h-8"
                  src="./spinner.gif"
                  alt="loader"
                />
              </div>
            </div>
            `,
          });
          // Building the project
          this.$axios
            .post("https://build.g-web.agency/api/build", {
              repo,
              domain,
              framework,
            })
            .then((res) => {
              // Replace the loader with the success message
              this.bashMsg[
                this.bashMsg.length - 1
              ].msg = `<div class="flex items-center">
                Project built successfully.
                <div class="ml-2 h-8 text-base relative top-1">
                  ✅
                </div>
              </div>`;
              // Deploying the project
              this.bashMsg.push({
                msg: `
                <div class="flex items-center">
                  <span>Deploying the project...</span>
                  <div class="ml-1 h-8">
                    <img
                      style="top: 1px"
                      class="relative w-8 h-8"
                      src="./spinner.gif"
                      alt="loader"
                    />
                  </div>
                </div>
                `,
              });
              this.$axios
                .post("https://build.g-web.agency/api/deploy", {
                  repo,
                  domain,
                  framework,
                  username: this.username,
                })
                .then((res) => {
                  // Replace the loader with the success message
                  console.log(res);
                  this.bashMsg[this.bashMsg.length - 1].msg = `
                    <div class="ml-2 h-8 text-base relative top-1">
                      ${res.data}
                    </div>
                  `;
                });
            })
            .catch((err) => {
              this.bashMsg.push({
                msg: "Error while building the project.",
              });
              console.log(err);
            });
        })
        .catch((err) => {
          this.bashMsg.push({
            msg: "Error while installing the dependencies.",
          });
        });
    },
    focus() {
      this.$refs.username.focus();
    },
    async checkDomain() {
      this.domainChecking = true;
      const res = await this.$axios.$get(
        `https://build.g-web.agency/api/check-domain/${this.domain}`
      );
      this.domainChecking = false;
      if (!res) {
        this.bashMsg.push({
          msg: `Domain <span class="text-pink-400">${this.domain}.g-web.agency</span> is available!`,
        });
        this.step = null;
        setTimeout(() => {
          this.bashMsg.push(
            { msg: `We are almost there, just a few more steps to go.` },
            { msg: `Which framework did you use?` }
          );
          this.step = 3;
        }, 1500);
      } else {
        this.step = 3;

        this.bashMsg.push({
          msg: `Domain <span class="text-pink-400">${this.domain}.g-web.agency</span> is not available!`,
        });
      }
    },
    selectRepository(i) {
      this.search = this.repository[i].name;
      this.repository = [];
    },
    chooseRepo() {
      this.bashMsg.push({
        msg: `You have chosen <span class="text-pink-400">${this.search}</span> repository. Now is time to choose a subdomain name.`,
      });
      this.step = 0;
      setTimeout(() => {
        this.step = 2;
      }, 1500);
    },
    searchRepositories() {
      // Seach in this.repos by name uppercase and lower case
      this.repository = this.repositories.filter((repo) => {
        return (
          repo.name.toUpperCase().includes(this.search.toUpperCase()) ||
          repo.name.toLowerCase().includes(this.search.toLowerCase())
        );
      });
    },
    async onEnter() {
      this.$axios
        .$get(`https://api.github.com/users/${this.username}/repos`)
        .then((repos) => {
          this.repositories = repos;
          this.usernameSet = true;
          this.step = 1;
          this.bashMsg.push({
            msg: `We found ${repos.length} repositories. Select which one you want to deploy.`,
          });
        })
        .catch((err) => {
          console.log(err);
          this.bashMsg.push({
            msg: `User not found.`,
          });
        });
    },
  },
};
</script>

<style>
@import url("https://fonts.googleapis.com/css2?family=Manrope:wght@300;500;800&family=Nabla&display=swap");

/* Font face */
@font-face {
  font-family: "Nabla";
  src: url("https://fonts.googleapis.com/css2?family=Manrope:wght@300;500;800&family=Nabla&display=swap");
}

.test {
  font-family: "Nabla";
}

.console {
  font-family: "Fira Mono";
  height: 350px;
  box-sizing: border-box;
  margin: auto;
}

.console header {
  border-top-left-radius: 15px;
  border-top-right-radius: 15px;
  background-color: #555;
  height: 45px;
  line-height: 45px;
  text-align: center;
  color: #ddd;
}

.console .consolebody {
  border-bottom-left-radius: 15px;
  border-bottom-right-radius: 15px;
  box-sizing: border-box;
  padding: 20px;
  height: calc(100% - 40px);
  overflow: scroll;
  background-color: rgba(0, 0, 0, 0.7);
  color: #63de00;
  font-size: 18px;
}

.console .consolebody p {
  line-height: 1.5rem;
}
/* 
.custom-gradient {
  background: -webkit-linear-gradient(
    45deg,
    #0a9ffe 0%,
    #0a9ffe 35%,
    #1124d9 70%,
    #1124d9 100%
  );
  background: -o-linear-gradient(
    45deg,
    #0a9ffe 0%,
    #0a9ffe 35%,
    #1124d9 70%,
    #1124d9 100%
  );
  background: linear-gradient(
    45deg,
    #0a9ffe 0%,
    #0a9ffe 35%,
    #1124d9 70%,
    #1124d9 100%
  );
} */

html,
body {
  height: 100%;
  background-color: #0ba376;
  background: -webkit-gradient(
    linear,
    left top,
    left bottom,
    from(#0b7080),
    to(#0ba376)
  );
  background: linear-gradient(#0b7080, #0ba376);
}

.custom-gradient {
  background-image: url("../static/main_bg.png");
  background-size: cover;
}
/*
  Enter and leave animations can use different
  durations and timing functions.
*/
.home-enter-active,
.home-leave-active {
  transition: all 0.3s ease-out;
  transition: all 0.8s cubic-bezier(1, 0.5, 0.8, 1);
}
.home-enter,
.home-leave-active {
  transform: translateX(20px);
  opacity: 0;
}
</style>
