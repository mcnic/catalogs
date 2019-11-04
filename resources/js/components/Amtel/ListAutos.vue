<template>
  <v-container>
    <v-breadcrumbs :items="bread" divider=">"></v-breadcrumbs>

    <v-list-item>
      <v-list-item-content>
        <v-list-item-title class="headline">Запчасти для {{ title }}</v-list-item-title>
      </v-list-item-content>
    </v-list-item>

    <AutoInfo
      v-for="auto in items"
      :key="auto.id"
      :id="auto.id"
      :image="auto.image"
      :text="auto.text"
      :url="auto.url"
    ></AutoInfo>
  </v-container>
</template>

<script>
import AutoInfo from "./AutoInfo";

export default {
  data: () => ({
    title: "B-MAX",
    model: "",
    bread: [
      {
        text: "Главная",
        disabled: false,
        href: "/"
      },
      {
        text: process.env.MIX_AMTEL_NAME,
        disabled: false,
        href: "/" + process.env.MIX_AMTEL_URL
      },
      {
        text: "Ford",
        disabled: false,
        href: "/" + process.env.MIX_AMTEL_URL + "/cars/ford"
      }
    ],
    items: [
      {
        id: "B-MAX 1",
        image: "https://file.amtel.club/v2/file/models/1565/1.png",
        text: "б/у: 479",
        url: "/bu/cars/ford/b-max/b-max1"
      },
      {
        id: "B-MAX 2",
        image: "https://file.amtel.club/v2/file/models/1565/1.png",
        text: "б/у: 479",
        url: "/bu/cars/ford/b-max/b-max2"
      },
      {
        id: "B-MAX 3",
        image: "https://file.amtel.club/v2/file/models/1565/1.png",
        text: "б/у: 479",
        url: "/bu/cars/ford/b-max/b-max3"
      },
      {
        id: "B-MAX 4",
        image: "https://file.amtel.club/v2/file/models/1565/1.png",
        text: "б/у: 479",
        url: "/bu/cars/ford/b-max/b-max4"
      }
    ]
  }),
  components: {
    AutoInfo
  },
  mounted() {
    this.model = this.$route.params.model;
    this.bread.push({
      text: this.title,
      disabled: true,
      href: "/" + process.env.MIX_AMTEL_URL + "/cars/ford/" + this.model
    });

    console.log("List autos");
  }
};
</script>
