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
    title: "Ford",
    firm: "",
    bread: [
      {
        text: "Каталоги",
        disabled: false,
        href: "/"
      },
      {
        text: process.env.MIX_AMTEL_NAME,
        disabled: false,
        href: "/" + process.env.MIX_AMTEL_PREFIX
      }
    ],
    items: [
      {
        id: "B-MAX",
        image: "https://file.amtel.club/v2/file/models/1565/1.png",
        text: "б/у: 479",
        url: "/" + process.env.MIX_AMTEL_PREFIX + "/cars/ford/b-max"
      },
      {
        id: "146",
        image: "https://file.amtel.club/v2/file/models/3/1.png",
        text: "б/у: 479",
        url: "/" + process.env.MIX_AMTEL_PREFIX + "/cars/acura/alfa2"
      },
      {
        id: "147",
        image: "https://file.amtel.club/v2/file/models/3/1.png",
        text: "б/у: 479",
        url: "/" + process.env.MIX_AMTEL_PREFIX + "/cars/acura/alfa3"
      },
      {
        id: "148",
        image: "https://file.amtel.club/v2/file/models/3/1.png",
        text: "б/у: 479",
        url: "/" + process.env.MIX_AMTEL_PREFIX + "/cars/acura/alfa4"
      }
    ]
  }),
  components: {
    AutoInfo
  },
  mounted() {
    this.firm = this.$route.params.firm;
    this.bread.push({
      text: this.title,
      disabled: true,
      href: "/" + process.env.MIX_AMTEL_PREFIX + "/cars/" + this.firm
    });

    console.log("List models");
  }
};
</script>
