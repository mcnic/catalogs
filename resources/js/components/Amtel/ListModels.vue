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
          href: "/" + pathArray[1] + "/" + pathArray[2] + "/" + pathArray[3]
        },
        {
          text: this.$store.getters.modelGroup,
          disabled: false,
          href: ""
        }
      ];
    },
    models($) {
      const list = this.$store.getters.models;
      if (this.$store.getters.debug) {
        console.log("computed models");
        console.log(list);
      }

      if (list.models.length == 0) {
        return;
      }

      const urlBase =
        "/" +
        process.env.MIX_AMTEL_PREFIX +
        "/" +
        this.$store.getters.typeAutos.toLowerCase() +
        "/" +
        this.$store.getters.firm.toLowerCase() +
        "/" +
        this.$store.getters.modelGroup.toLowerCase() +
        "/";

      let models = [];
      let data = "";
      let goods_sh_avail = 0;
      if (list.avail.result == true) {
        list.models.forEach(el => {
          let goods_sh_avail = list.avail.model_list.find(function(
            item,
            index,
            array
          ) {
            if (item.model_id == el.id) {
              return true;
            }
          });

          if (goods_sh_avail != undefined) {
            if (el.start == null) {
              data = "<" + el.end;
            } else {
              if (el.end == null) {
                data = el.start + ">";
              } else {
                data = el.start + "-" + el.end;
              }
            }

            models.push({
              id: el.id,
              name: el.title + " " + data,
              image: el.image == "" ? "/images/noauto.png" : el.image,
              text: "б/у з/ч: " + goods_sh_avail.goods_sh_avail,
              url: urlBase + el.url
            });
          }
        });
        return models.length == 0 ? "" : models;
      }
    }
  },
  mounted() {
    if (this.$store.getters.debug) {
      console.log("List models");
      console.log(pathArray);
    }

    const pathArray = this.$route.path.split("/");
    this.$store.dispatch("setTypeAutos", pathArray[2]);
    this.$store.dispatch("setFirm", pathArray[3]);
    this.$store.dispatch("setModelGroup", pathArray[4]);

    this.$store.dispatch("renewModels");
  }
};
</script>
