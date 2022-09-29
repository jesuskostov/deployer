<!-- Please remove this file from your project -->
<template>
  <div
    class="flex items-center justify-center relative w-full min-h-screen sm:pt-0 custom-gradient"
  >
    <div
      style="height: 540px"
      class="grid grid-cols-7 w-8/12 bg-white rounded-xl overflow-hidden"
    >
      <div v-if="userinfo" class="col-span-2 h-full bg-gray-700 text-white">
        <div class="relative profile-image">
          <div
            class="w-32 h-32 rounded-full mx-auto mt-14 overflow-hidden border border-gray-400"
          >
            <img
              class="w-full h-full object-cover"
              :src="userinfo.avatar_url"
              alt=""
            />
          </div>
        </div>
        <h1 class="text-center text-xl mt-2 mb-6">{{ userinfo.login }}</h1>
        <p class="flex justify-between w-full text-sm px-6 mb-2">
          Public repos: <span>{{ userinfo.public_repos }}</span>
        </p>
        <p class="flex justify-between w-full text-sm px-6 mb-2">
          Followers: <span>{{ userinfo.followers }}</span>
        </p>
        <p class="flex justify-between w-full text-sm px-6 mb-2">
          Github since:
          <span>
            <!-- How old is the account -->
            {{ new Date(userinfo.created_at).getFullYear() }}
          </span>
        </p>
        <p class="flex justify-between w-full text-sm px-6 mb-2">
          Github link:
          <span class="underline"
            ><a :href="userinfo.html_url" target="_blank">here</a></span
          >
        </p>
      </div>
      <div class="col-span-5 py-5 px-5 overflow-scroll">
        <div class="grid grid-cols-3 gap-x-4 w-full h-full">
          <div
            v-for="(repo, i) in repositories"
            :key="i"
            class="col-span-1 p-4 border border-gray-500 rounded-lg h-12 flex items-center opacity-50 cursor-pointer hover:opacity-100 hover:text-green-500 hover:bg-green-100 hover:border-green-500 transition-all duration-300"
          >
            {{ repo.name }}
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
      username: "jesuskostov",
      userinfo: null,
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
  mounted() {
    this.onEnter();
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
                      src="../pages/spinner.gif"
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
      const data = await this.$axios.$get(
        `https://api.github.com/users/${this.username}`
      );
      this.userinfo = data;
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
  background-color: #000;
  color: #63de00;
}

.console .consolebody p {
  line-height: 1.5rem;
}

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
