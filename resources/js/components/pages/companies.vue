<template>
  <div>
    <v-snackbar :color="snackbar.color" v-model="snackbar.show">
      {{ snackbar.text }}

      <template v-slot:action="{ attrs }">
        <v-btn color="white" text v-bind="attrs" @click="snackbar.show = false">
          Close
        </v-btn>
      </template>
    </v-snackbar>

    <v-dialog v-model="addCompanyDialog" max-width="600px">
      <v-form ref="form" v-model="form" lazy-validation>
        <v-card>
          <v-toolbar
            src="https://cdn.vuetifyjs.com/images/backgrounds/bg-2.jpg"
            color="primary"
            dark
            >Add Company

            <v-spacer></v-spacer>
            <v-btn icon>
              <v-icon
                large
                class="white--text"
                @click="addCompanyDialog = false"
                >mdi-close
              </v-icon></v-btn
            >
          </v-toolbar>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    filled
                    v-model="name"
                    rounded
                    label="name*"
                    :error-messages="errors.name"
                    :rules="[(v) => !!v || 'Name is required']"
                    required
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              color="blue darken-1"
              class="px-8 py-2"
              large
              :loading="loadingButton"
              rounded
              dark
              @click="storeCompany"
            >
              Save
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-dialog>

    <v-row>
      <v-col cols="3">
        <v-btn @click="addCompanyDialog = true" class="primary mx-4 my-4" dark
          >Add New Company</v-btn
        >
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12">
        <v-data-table
          :headers="headers"
          :items="companies"
          dense
          :server-items-length="totalCompanies"
          :loading="loading"
          loading-text="Loading... Please wait"
          :options.sync="options"
          item-key="id"
          class="elevation-6"
        >
        </v-data-table>
      </v-col>
    </v-row>
  </div>
</template>


<script>
export default {
  mounted() {
    this.getcompanies();
  },
  watch: {
    options: {
      handler() {
        this.getcompanies();
      },
      deep: true,
    },
    globalsearch: {
      handler() {
        this.getcompanies();
      },
      deep: true,
    },
  },

  data() {
    return {
      search: "",
      name: "",
      form: "",
      errors: [],
      totalCompanies: null,
      options: {},
      loading: false,
      loadingButton: false,
      companies: [],
      snackbar: {
        show: false,
        text: "test",
        color: "green",
      },
      addCompanyDialog: false,
      headers: [
        { text: "ID", value: "id" },
        { text: "Name", value: "name" },
      ],
    };
  },
  methods: {
    getcompanies() {
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;
      let payload = {};
      payload = {
        page: page,
        per_page: itemsPerPage,
      };

      if (sortBy.length === 1 && sortDesc.length === 1) {
        if (sortDesc[0]) {
          const direction = "desc";
          payload.direction = direction;
          payload.sortBy = sortBy[0];
        } else {
          const direction = "asc";
          payload.direction = direction;
          payload.sortBy = sortBy[0];
        }
      }

      axios
        .get(`/api/companies`, {
          params: payload,
        })
        .then((res) => {
          this.companies = res.data.data;
          this.totalCompanies = res.data.total;
        })
        .catch((err) => console.log(err.response.data))
        .finally(() => (this.loading = false));

      // get by search
    },
    storeCompany() {
      if (!this.$refs.form.validate()) return;

      this.loadingButton = true;
      axios
        .post("/api/companies", {
          name: this.name,
        })
        .then((response) => {
          this.loadingButton = false;
          if (response.status == 201) {
            this.snackbar.text = "company saved";
            this.snackbar.color = "green";
            this.snackbar.show = true;
            this.getcompanies();
            this.addCompanyDialog = false;
          }
        })
        .catch((err) => {
          this.loadingButton = false;
          if (err.response.status == 422) {
            this.errors = err.response.data.errors;
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

<style >
.v-data-table table th {
  font-size: 12px !important;
  background-color: #2980d5;
  padding: 10px !important;
  color: #fff !important;
}

.v-data-table table td {
  font-size: 13px !important;
  text-align: left;
  padding: 12px !important;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
    "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji",
    "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
}
</style>