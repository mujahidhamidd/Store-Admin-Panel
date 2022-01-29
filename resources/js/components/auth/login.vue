<template>
  <v-row align="center" justify="center" class="my-9 py-9">


    <v-snackbar :color="snackbar.color" v-model="snackbar.show">
      {{ snackbar.text }}

      <template v-slot:action="{ attrs }">
        <v-btn color="white" text v-bind="attrs" @click="snackbar.show = false">
          Close
        </v-btn>
      </template>
    </v-snackbar>
    <v-col cols="12" sm="8" md="4">
      <v-form ref="form" v-model="form" lazy-validation>
        <v-card class="elevation-12">
          <v-toolbar color="primary" dark flat>
            <v-toolbar-title>Login</v-toolbar-title>
            <div class="flex-grow-1"></div>
          </v-toolbar>
          <v-card-text>
            <v-text-field
              v-model="email"
              label="Login"
              :error-messages="errors.email"
              :rules="[(v) => !!v || 'Item is required']"
              name="login"
              prepend-icon="mdi-account"
              type="text"
            ></v-text-field>

            <v-text-field
              v-model="password"
              id="password"
              label="Password"
              :error-messages="errors.password"
              name="password"
              :rules="[(v) => !!v || 'Item is required']"
              prepend-icon="mdi-lock"
              type="password"
            ></v-text-field>
          </v-card-text>
          <v-card-actions>
            <div class="flex-grow-1"></div>
            <v-btn @click="login" color="primary">Login</v-btn>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-col>
  </v-row>
</template>

<script>
import {
  saveUserToLocalStorage,
  setAutorizationHeaders,
} from "../../helpers/helpers";

export default {
  props: {
    source: String,
  },

  data: () => ({
    drawer: null,
    email: "",
    errors: [],
    password: "",
    form: false,
    snackbar: {
      show: false,
      text: "test",
      color: "green",
    },
  }),
  methods: {
    login() {
      if (!this.$refs.form.validate()) return;
      axios
        .post("/api/login", {
          email: this.email,
          password: this.password,
        })
        .then((response) => {
          // svae user and token into local storage
          if (response.status == 200) {
            saveUserToLocalStorage(response.data.userInfo);
            setAutorizationHeaders(response.data.userInfo.token);

            window.location.href = "/companies"

          }
        })
        .catch((err) => {
          // handle validation errors
          if (err.response.status == 422) {
            this.errors = err.response.data.errors;
          } else if (err.response.status == 401) {
            this.snackbar.text = err.response.data.msg;
            this.snackbar.color = "red";
            this.snackbar.show = true;
          } else {
            this.snackbar.text = "sorry something went wrong !!";
            this.snackbar.color = "red";
            this.snackbar.show = true;
          }
        });
    },
  },
};
</script>
