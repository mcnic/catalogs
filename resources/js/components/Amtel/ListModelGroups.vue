<template>
  <v-container>
    <v-breadcrumbs :items="breadCrumbs" divider=">"></v-breadcrumbs>

    <v-list-item>
      <v-list-item-content>
        <v-list-item-title class="headline">Запчасти для {{ title }}</v-list-item-title>
      </v-list-item-content>
    </v-list-item>

    <div v-if="modelGroups == ''">Предложения отсутствуют</div>

    <AutoInfo
      v-else
      v-for="model in modelGroups"
      :key="model.id"
      :id="model.id"
      :name="model.name"
      :image="model.image"
      :text="model.text"
      :url="model.url"
    ></AutoInfo>
  </v-container>
</template>

<script>
export default {
  components: {
    AutoInfo: () => import("./AutoInfo")
  },
  data: () => ({}),
  computed: {
    title($) {
      return this.$store.getters.firm;
    },
    breadCrumbs($) {
      this.$store.getters.debug ? console.log("comp breadCrumbs") : "";
      const pathArray = this.$route.path.split("/");
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
          href: ""
        }
        /*{
          text: this.$store.getters.model,
          disabled: false,
          href: pathArray[1]
        }*/
      ];
    },
    modelGroups($) {
      const list = this.$store.getters.modelGroups;
      if (this.$store.getters.debug) {
        console.log("computed modelGroups");
        console.log(list);
      }

      const urlBase =
        "/" +
        process.env.MIX_AMTEL_PREFIX +
        "/" +
        this.$store.getters.typeAutos.toLowerCase() +
        "/" +
        this.$store.getters.firm.toLowerCase() +
        "/";

      let models = [];
      let data = "";
      if (list.length == 0) {
        return;
      }
      list.forEach(el => {
        models.push({
          id: el.title,
          name: el.title,
          image: el.img == "" ? "/images/noauto.png" : el.img,
          text: "",
          url:
            urlBase +
            //el.model_name.replace(/\//g, "-")
            el.title
        });
      });
      return models.length == 0 ? "" : models;
    }
  },
  mounted() {
    if (this.$store.getters.debug) {
      console.log("List modelGroups");
      console.log(pathArray);
    }

    const pathArray = this.$route.path.split("/");
    this.$store.dispatch("setTypeAutos", pathArray[2]);
    this.$store.dispatch("setFirm", pathArray[3]);
    //this.$store.dispatch("setModel", pathArray[3]);

    this.$store.dispatch("renewModelGroups");
  }
};
</script>
