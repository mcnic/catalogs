<template>
  <v-container>
    <v-breadcrumbs :items="bread" divider=">"></v-breadcrumbs>

    <v-list-item>
      <v-list-item-content>
        <v-list-item-title class="headline">Запчасти для {{ title }}</v-list-item-title>
      </v-list-item-content>
    </v-list-item>

    <div>list Goods</div>

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
    title: "B-MAX 1",
    auto: "",
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
      },
      {
        text: "B-MAX",
        disabled: false,
        href: "/" + process.env.MIX_AMTEL_URL + "/cars/ford/b-max"
      }
    ],
    items: []
  }),
  components: {
    AutoInfo
  },
  mounted() {
    this.auto = this.$route.params.model;
    this.bread.push({
      text: this.title,
      disabled: true,
      href: "/" + process.env.MIX_AMTEL_URL + "/cars/ford/b-max" + this.auto
    });

    console.log("Goods");
  }
};
</script>
