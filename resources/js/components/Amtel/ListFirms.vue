<template>
  <v-container>
    <v-breadcrumbs :items="bread" divider=">"></v-breadcrumbs>

    <v-list-item>
      <v-list-item-content>
        <v-list-item-title class="headline">Поиск по марке</v-list-item-title>
      </v-list-item-content>
    </v-list-item>

    <v-tabs v-model="firmTab">
      <v-tab>Легковые автомобили</v-tab>
      <v-tab>Грузовые автомобили</v-tab>

      <v-tab-item>
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
export default {
  data: () => ({
    firmTab: 0,
    mainUrl: process.env.MIX_AMTEL_URL,
    bread: [
      {
        text: "Главная",
        disabled: false,
        href: "/"
      },
      {
        text: process.env.MIX_AMTEL_NAME,
        disabled: true,
        href: "/" + process.env.MIX_AMTEL_URL
      }
    ],
    lightCars: [
      { title: "Acura", url: "/bu/cars/acura" },
      { title: "Ford", url: "/bu/cars/ford" }
    ],
    trucks: [
      { title: "BAW", url: "/bu/trucks/baw" },
      { title: "BPW", url: "/bu/trucks/bpw" }
    ]
  }),
  mounted() {
    console.log("List firms");
  }
};
</script>
