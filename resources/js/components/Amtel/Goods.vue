<template>
  <v-container>
    <v-breadcrumbs :items="breadCrumbs" divider=">"></v-breadcrumbs>

    <v-row>
      <v-col cols="6">
        <v-text-field
          v-model="search"
          placeholder="быстрый поиск по каталогу"
          hide-details
          clearable
          clear-icon="mdi-close-circle-outline"
        ></v-text-field>

        <v-treeview hoverable open-on-click :items="items" :search="search">
          <template slot="label" slot-scope="props">
            <router-link :to="props.item.to" v-if="props.item.to">{{ props.item.name }}</router-link>
            <span v-else>{{ props.item.name }}</span>
          </template>
        </v-treeview>
      </v-col>

      <v-col cols="6">
        <v-list-item>
          <v-list-item-content>
            <v-list-item-title class="headline">Запчасти для {{ title }}</v-list-item-title>
            <div>list Goods</div>
          </v-list-item-content>
        </v-list-item>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  components: {
    AutoInfo: () => import("./AutoInfo")
  },
  data: () => ({
    search: "",
    items: [
      {
        id: 1,
        name: "Applications :",
        children: [
          { id: 2, name: "Calendar : app", to: "/url" },
          { id: 3, name: "Chrome : app", to: "/url" },
          { id: 4, name: "Webstorm : app", to: "/url" }
        ]
      },
      {
        id: 5,
        name: "Documents :",
        children: [
          {
            id: 6,
            name: "vuetify :",
            children: [
              {
                id: 7,
                name: "src :",
                children: [
                  { id: 8, name: "index : ts" },
                  { id: 9, name: "bootstrap : ts" }
                ]
              }
            ]
          },
          {
            id: 10,
            name: "material2 :",
            children: [
              {
                id: 11,
                name: "src :",
                children: [
                  { id: 12, name: "v-btn : ts" },
                  { id: 13, name: "v-card : ts" },
                  { id: 14, name: "v-window : ts" }
                ]
              }
            ]
          }
        ]
      }
    ]
  }),
  computed: {
    title($) {
      return this.$store.getters.model;
    },
    breadCrumbs($) {
      const pathArray = this.$route.path.split("/");
      console.log("comp breadCrumbs");
      //console.log(pathArray);

      return [
        {
          text: "Главная",
          disabled: false,
          href: "/"
        },
        {
          text: process.env.MIX_AMTEL_NAME,
          disabled: false,
          href: "/" + pathArray[1]
        },
        {
          text: this.$store.getters.firm,
          disabled: false,
          href: "/" + pathArray[1] + "/" + pathArray[2] + "/" + pathArray[3]
        },
        {
          text: this.$store.getters.modelGroup,
          disabled: false,
          href:
            "/" +
            pathArray[1] +
            "/" +
            pathArray[2] +
            "/" +
            pathArray[3] +
            "/" +
            pathArray[4]
        },
        {
          text: this.$store.getters.model,
          disabled: false,
          href: ""
        }
      ];
    }
  },
  mounted() {
    if (this.$store.getters.debug) {
      console.log("Goods");
      //console.log(pathArray);
    }

    const pathArray = this.$route.path.split("/");
    this.$store.dispatch("setTypeAutos", pathArray[2]);
    this.$store.dispatch("setFirm", pathArray[3]);
    this.$store.dispatch("setModelGroup", pathArray[4]);
    this.$store.dispatch("setModel", pathArray[5]);

    //this.$store.dispatch("renewGoods");
  }
};
</script>
