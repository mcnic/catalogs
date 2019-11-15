<template>
  <v-container>
    <v-breadcrumbs :items="breadCrumbs" divider=">"></v-breadcrumbs>

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
export default {
  components: {
    AutoInfo: () => import("./AutoInfo")
  },
  data: () => ({
    title: "",
    firm: "",
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
  computed: {
    breadCrumbs(state) {
      return this.$store.getters.breadCrumbs;
    }
  },
  mounted() {
    this.$store.getters.debug ? console.log("List models") : "";

    this.firm = this.$route.params.firm; //todo
    this.title = this.$store.getters.titleFirm(this.firm); //todo

    const routeArray = this.$router.options.routes;
    //const pathArray = this.$route.path.split("/", 3);
    const pathArray = this.$route.path.split("/");
    const addBread = {
      text: this.title,
      disabled: true,
      href: ""
    };

    this.$store.dispatch("renewBreadCrumbs", {
      routeArray,
      pathArray,
      addBread
    });
  }
};
</script>
