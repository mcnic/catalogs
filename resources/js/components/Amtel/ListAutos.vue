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
    title: "B-MAX",
    model: "",
    items: [
      {
        id: "B-MAX 1",
        image: "https://file.amtel.club/v2/file/models/1565/1.png",
        text: "б/у: 479",
        url: "/" + process.env.MIX_AMTEL_PREFIX + "/cars/ford/b-max/b-max1"
      },
      {
        id: "B-MAX 2",
        image: "https://file.amtel.club/v2/file/models/1565/1.png",
        text: "б/у: 479",
        url: "/" + process.env.MIX_AMTEL_PREFIX + "/cars/ford/b-max/b-max2"
      },
      {
        id: "B-MAX 3",
        image: "https://file.amtel.club/v2/file/models/1565/1.png",
        text: "б/у: 479",
        url: "/" + process.env.MIX_AMTEL_PREFIX + "/cars/ford/b-max/b-max3"
      },
      {
        id: "B-MAX 4",
        image: "https://file.amtel.club/v2/file/models/1565/1.png",
        text: "б/у: 479",
        url: "/" + process.env.MIX_AMTEL_PREFIX + "/cars/ford/b-max/b-max4"
      }
    ]
  }),
  computed: {
    breadCrumbs($) {
      const pathArray = this.$route.path.split("/");
      console.log("comp breadCrumbs");
      console.log(pathArray);

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
          href:
            "/" +
            pathArray[1] +
            "/" +
            this.$store.getters.typeAutos +
            "/" +
            pathArray[3]
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
    this.$store.getters.debug ? console.log("List autos") : "";
    console.log(pathArray);

    const pathArray = this.$route.path.split("/");
    this.$store.dispatch("setTypeAutos", pathArray[2]);
    this.$store.dispatch("setFirm", pathArray[3]);
    this.$store.dispatch("setModel", pathArray[4]);

    /*this.model = this.$route.params.model; //todo

    const routeArray = this.$router.options.routes;
    const pathArray = this.$route.path.split("/", 4);
    const addBread = {
      text: this.title,
      disabled: true,
      href: "/" + process.env.MIX_AMTEL_PREFIX + "/cars/ford/" + this.model
    };

    this.$store.dispatch("renewBreadCrumbs", {
      routeArray,
      pathArray,
      addBread
    });*/
  }
};
</script>
