<template>
  <v-container>
    <v-breadcrumbs :items="breadCrumbs" divider=">"></v-breadcrumbs>

    <v-list-item>
      <v-list-item-content>
        <v-list-item-title class="headline">Запчасти для {{ title }}</v-list-item-title>
      </v-list-item-content>
    </v-list-item>

    <div v-if="models == ''">Предложения отсутствуют</div>

    <AutoInfo
      v-else
      v-for="model in models"
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
          href: ""
        }
        /*{
          text: this.$store.getters.model,
          disabled: false,
          href: pathArray[1]
        }*/
      ];
    },
    models($) {
      const list = this.$store.getters.models;
      if (this.$store.getters.debug) {
        console.log("computed models");
        //console.log(list);
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
      if (list.result == true) {
        list.model_list.forEach(el => {
          if (el.goods_sh_avail > 0) {
            if (el.model_year_start == null) {
              data = "<" + el.model_year_end;
            } else {
              if (el.model_year_end == null) {
                data = el.model_year_start + ">";
              } else {
                data = el.model_year_start + "-" + el.model_year_end;
              }
            }

            models.push({
              id: el.model_id,
              name: el.model_name + " " + data,
              image:
                list.model_image_list[el.model_id] == undefined
                  ? "/images/noauto.png"
                  : list.model_image_list[el.model_id][0].url,
              text: "б/у з/ч: " + el.goods_sh_avail,
              url:
                urlBase +
                //el.model_name.replace(/\//g, "-")
                el.model_id
            });
          }
        });
        return models.length == 0 ? "" : models;
      }
    }
  },
  mounted() {
    this.$store.getters.debug ? console.log("List models") : "";
    console.log("List models");
    console.log(pathArray);

    const pathArray = this.$route.path.split("/");
    this.$store.dispatch("setTypeAutos", pathArray[2]);
    this.$store.dispatch("setFirm", pathArray[3]);
    //this.$store.dispatch("setModel", pathArray[3]);

    this.$store.dispatch("renewModels");
  }
};
</script>
