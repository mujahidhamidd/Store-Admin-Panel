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
    <v-dialog v-model="addCategoryDialog" persistent max-width="600px">
      <v-form ref="form" v-model="form" lazy-validation>
        <v-card>
          <v-toolbar
            src="https://cdn.vuetifyjs.com/images/backgrounds/bg-2.jpg"
            color="primary"
            dark
            >Add Category

            <v-spacer></v-spacer>
            <v-btn icon>
              <v-icon
                large
                class="white--text"
                @click="addCategoryDialog = false"
                >mdi-close
              </v-icon></v-btn
            >
          </v-toolbar>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    v-model="name"
                    label="name*"
                    filled
                    rounded
                    :error-messages="errors.name"
                    :rules="[(v) => !!v || 'Item is required']"
                    required
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>

            <v-btn
              large
              dark
              :loading="loadingButton"
              rounded
              color="blue darken-1"
              class="px-8 py-2"
              @click="storeCategory"
            >
              Save
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-dialog>

    <v-row>
      <v-col cols="3">
        <v-btn @click="addCategoryDialog = true" class="primary mx-4 my-4" dark
          >Add New Category
          <span v-if="active.length > 0"> Under {{ active[0].name }} </span>
        </v-btn>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="6">
        <v-treeview
          :active.sync="active"
          :return-object="true"
          :items="categories"
          activatable
          rounded
          hoverable
          color="primary"
          transition
        >
        </v-treeview>
      </v-col>
    </v-row>
  </div>
</template>


<script>
export default {
  mounted() {
    this.getCategories();
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
        this.getCategories();
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
      active: [],
      categories: [],
      snackbar: {
        show: false,
        text: "test",
        color: "green",
      },
      addCategoryDialog: false,
      loadingButton: false,
    };
  },
  methods: {
    getCategories() {
      axios
        .get(`/api/categories`)
        .then((res) => {
          this.categories = res.data.allCategoriesTree;
        })
        .catch((err) => console.log(err.response.data))
        .finally(() => ({}));

      // get by search
    },
    storeCategory() {
      if (!this.$refs.form.validate()) return;

      this.loadingButton = true;
      axios
        .post("/api/categories", {
          name: this.name,
          parent_id: this.active.length > 0 ? this.active[0].id : null,
        })
        .then((response) => {
          if (response.status == 201) {
            this.loadingButton = false;
            this.snackbar.text = "Category saved";
            this.snackbar.color = "green";
            this.snackbar.show = true;
            this.getCategories();
            this.addCategoryDialog = false;
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