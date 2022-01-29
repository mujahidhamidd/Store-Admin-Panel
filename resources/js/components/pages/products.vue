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
    <v-dialog v-model="addProductDialog" persistent max-width="1000px">
      <v-form ref="form" v-model="form" lazy-validation>
        <v-card>
           <v-toolbar
            src="https://cdn.vuetifyjs.com/images/backgrounds/bg-2.jpg"
            color="primary"
            dark
            >Add Product

            <v-spacer></v-spacer>
            <v-btn icon>
              <v-icon
                large
                class="white--text"
                @click="addProductDialog = false"
                >mdi-close
              </v-icon></v-btn
            >
          </v-toolbar>
            <v-container>
              <v-row>
                <v-col cols="6">
                  <v-text-field
                    rounded
                    filled
                    v-model="name"
                    class="mx-4 white--text"
                    label="name*"
                    :error-messages="errors.name"
                    :rules="[(v) => !!v || 'Name is required']"
                    required
                  ></v-text-field>
                </v-col>

                <v-col cols="6">
                  <v-text-field
                    v-model="price"
                    label="Price*"
                    type="number"
                    rounded
                    filled
                    class="mx-4 white--text"
                    :error-messages="errors.price"
                    :rules="[(v) => !!v || 'Price is required']"
                    required
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-autocomplete
                    v-model="selectedCompany"
                    :items="companies"
                    :error-messages="errors.category_id"
                    chips
                    rounded
                    filled
                    dense
                    item-text="name"
                    item-value="id"
                    class="mx-4 white--text"
                    label="Select Company"
                  >
                  </v-autocomplete>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12">
                   <p class="text--h4 mx-4"> Select Category:  </p>
                  <v-treeview
                    :active.sync="selectedCategory"
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
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
           
            <v-btn :loading="loadingButton" color="blue darken-1" dark rounded class="px-8 py-2" @click="storeProduct">
              Save
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-dialog>

    <v-row>
      <v-col cols="3">
        <v-btn @click="addProductDialog = true" class="primary mx-4 my-4" dark
          >Add New Product</v-btn
        >
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12">
        <v-data-table
          :headers="headers"
          :items="products"
          :server-items-length="totalproducts"
          :loading="loading"
          loading-text="Loading... Please wait"
          :options.sync="options"
          item-key="id"
          class="elevation-1"
        >
        </v-data-table>
      </v-col>
    </v-row>
  </div>
</template>


<script>
export default {
  mounted() {
    this.getProducts();
    this.getCompanies();
    this.getCategories();
  },
  watch: {
    options: {
      handler() {
        this.getProducts();
      },
      deep: true,
    },
    globalsearch: {
      handler() {
        this.getProducts();
      },
      deep: true,
    },
  },

  data() {
    return {
      search: "",
      name: "",
      price: 0,
      form: "",
      loadingButton : false,
      errors: [],
      totalproducts: null,
      options: {},
      loading: false,
      products: [],
      companies: [],
      categories: [],
      selectedCategory: null,
      selectedCompany: null,
      snackbar: {
        show: false,
        text: "test",
        color: "green",
      },
      addProductDialog: false,
      headers: [
        { text: "ID", value: "id" },
        { text: "Name", value: "name" },
        { text: "Price", value: "price" },
        { text: "Company", value: "company.name" },
        { text: "Category", value: "category_text" },
      ],
    };
  },
  methods: {
    getProducts() {
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
        .get(`/api/products`, {
          params: payload,
        })
        .then((res) => {
          this.products = res.data.data;
          this.totalproducts = res.data.total;
        })
        .catch((err) => console.log(err.response.data))
        .finally(() => (this.loading = false));

      // get by search
    },

    getCategories() {
      axios
        .get(`/api/categories`)
        .then((res) => {
          this.categories = res.data;
        })
        .catch((err) => console.log(err.response.data))
        .finally(() => ({}));

      // get by search
    },
    getCompanies() {
      axios
        .get(`/api/companies`)
        .then((res) => {
          this.companies = res.data.data;
        })
        .catch((err) => console.log(err.response.data))
        .finally(() => ({}));

      // get by search
    },
    storeProduct() {
      if (!this.$refs.form.validate()) return;
       this.loadingButton = true;
      axios
        .post("/api/products", {
          name: this.name,
          price: this.price,
          category_id: this.selectedCategory[0],
          company_id: this.selectedCompany,
        })
        .then((response) => {
           this.loadingButton = false;
          if (response.status == 201) {
            this.snackbar.text = "Product saved";
            this.snackbar.color = "green";
            this.snackbar.show = true;
            this.getProducts();
            this.addProductDialog = false;
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