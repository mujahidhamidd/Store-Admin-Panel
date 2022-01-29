<template>
  <v-app id="inspire">
    <div v-if="userInfo">
      <v-navigation-drawer
        v-model="drawer"
        app
        class="py-6"
        clipped
        src="https://cdn.vuetifyjs.com/images/backgrounds/bg-2.jpg"
        dark
      >
        <v-list dense>
          <v-list-item
            v-for="item in items"
            :key="item.text"
            @click="pushLink(item.link)"
          >
            <v-list-item-action>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>
                {{ item.text }}
              </v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </v-navigation-drawer>

      <v-app-bar
        :clipped-left="$vuetify.breakpoint.lgAndUp"
        app
        src="https://cdn.vuetifyjs.com/images/backgrounds/bg-2.jpg"
        dark
      >
        <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
        <v-toolbar-title style="width: 300px" class="ml-0 pl-4">
          <span class="hidden-sm-and-down">Store Admin Panel</span>
        </v-toolbar-title>

        <v-spacer> </v-spacer>

        <v-menu left bottom offset-y>
          <template v-slot:activator="{ on }">
            <v-btn icon v-on="on">
              <v-avatar color="orange" size="32px">
                {{ userInfo.user.name.charAt(0).toUpperCase() }}
              </v-avatar>
            </v-btn>
          </template>
          <v-list>
            <v-list-item link>
              <v-list-item-avatar color="orange">
                {{ userInfo.user.name.charAt(0).toUpperCase() }}
              </v-list-item-avatar>
              <v-list-item-content>
                <v-list-item-title class="title">{{
                  userInfo.user.name
                }}</v-list-item-title>
                <v-list-item-subtitle>{{
                  userInfo.user.email
                }}</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-list>
          <v-divider></v-divider>
          <v-list nav dense>
            <v-list-item-group color="primary">
              <v-divider></v-divider>
              <v-list-item @click="logout">
                <v-list-item-icon>
                  <v-icon color="red">mdi-logout</v-icon>
                </v-list-item-icon>
                <v-list-item-title>LOGOUT</v-list-item-title>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-menu>
      </v-app-bar>
    </div>
    <v-main class="mx-8 my-8">
      <router-view></router-view>
    </v-main>
  </v-app>
</template>

<script>
import { getLoggedInUser } from "../../helpers/helpers";

export default {
  name: "home",
  props: {
    source: String,
  },
  data: () => ({
    dialog: false,
    drawer: null,
    userInfo: getLoggedInUser(),
    items: [
      { icon: "mdi-post", text: "Companies", link: "/companies" },
      { icon: "mdi-note", text: "Categories", link: "/categories" },
      { icon: "mdi-cart", text: "Products", link: "/products" },
    ],
  }),
  methods: {
    logout() {
      localStorage.removeItem("userInfo");
      window.location.href = "/login";
    },
    pushLink(link) {
      this.$router.push(link);
    },
  },
};
</script>
