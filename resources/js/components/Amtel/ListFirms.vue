<template>
  <v-container>
    <v-breadcrumbs :items="breadCrumbs" divider=">"></v-breadcrumbs>

    <v-list-item>
      <v-list-item-content>
        <v-list-item-title class="headline">Поиск по марке</v-list-item-title>
      </v-list-item-content>
    </v-list-item>

    <v-tabs v-model="firmTab">
      <v-tab>Легковые автомобили</v-tab>
      <v-tab>Грузовые автомобили</v-tab>

      <v-tab-item light show-arrows>
        <v-card flat>
          <v-card-text>
            <div v-for="item in lightCars" :key="item.title">
              <a :href="item.url">{{ item.title }}</a>
            </div>
          </v-card-text>
        </v-card>
      </v-tab-item>

      <v-tab-item>
        <v-card flat>
          <v-card-text>
            <div v-for="item in trucks" :key="item.title">
              <a :href="item.url">{{ item.title }}</a>
            </div>
          </v-card-text>
        </v-card>
      </v-tab-item>
    </v-tabs>
  </v-container>
</template>

<script>
//import { VTabs } from "vuetify/lib";

export default {
  data: () => ({
    firmTab: 0,
    mainUrl: process.env.MIX_AMTEL_PREFIX
  }),
  computed: {
    /*breadCrumbs(state) {
      return this.$store.getters.breadCrumbs;
    },*/
    lightCars($) {
      return this.$store.getters.lightCars;
    },
    trucks($) {
      return this.$store.getters.trucks;
    },
    breadCrumbs($) {
      const pathArray = this.$route.path.split("/");

      return [
        {
          text: "Главная",
          disabled: false,
          href: "/"
        },
        {
          text: process.env.MIX_AMTEL_NAME,
          disabled: false,
          href: ""
        }
      ];
    }
  },
  methods: {},
  mounted() {
    this.$store.getters.debug ? console.log("List firms") : "";
    this.$store.dispatch("renewFirms");
  }
};
</script>
