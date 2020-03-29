<template>
  <v-container>
    <v-breadcrumbs :items="breadCrumbs" divider=">"></v-breadcrumbs>

    <v-row>
      <v-col cols="4">
        <v-text-field
          v-model="search"
          placeholder="быстрый поиск по каталогу"
          hide-details
          clearable
          clear-icon="mdi-close-circle-outline"
        ></v-text-field>

        <v-treeview
          hoverable
          rounded
          openOnClick
          activatable
          dense
          :items="listGoods"
          :search="search"
          @update:active="onInput"
        >
          <!--template slot="label" slot-scope="props">
            <router-link :to="props.item.to" v-if="props.item.to">{{ props.item.name }}</router-link>
            <span v-else>{{ props.item.name }}</span>
          </template-->
        </v-treeview>
      </v-col>

      <v-col cols="8">
        <v-list-item>
          <v-list-item-content>
            <v-list-item-title class="headline">Запчасти для {{ title }}</v-list-item-title>
            <div v-if="goods == ''">Предложения отсутствуют</div>
            <v-container v-else class="grey lighten-5">
              <GoodInfo
                v-for="good in goods"
                :key="good.id"
                :id="good.id"
                :comment="good.comment"
                :name="good.name"
                :company="good.company_name"
                :num="good.num"
                :avail="good.avail"
                :price="good.price"
                :wearout="good.wearout"
                :img="good.img"
              ></GoodInfo>
            </v-container>
          </v-list-item-content>
        </v-list-item>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  components: {
    GoodInfo: () => import("./GoodInfo")
  },
  data: () => ({
    search: "",
    goodsId: undefined,
    shipping_company: {}
  }),
  methods: {
    onInput(arr) {
      //this.goods = arr[0];
      this.goodsId = arr[0];
      this.$store.dispatch("getGoods", this.goodsId);
    }
  },
  computed: {
    title($) {
      return this.$store.getters.model;
    },
    goods($) {
      this.$store.getters.debug ? console.log("comp goods") : "";
      console.log(this.goodsId);

      return this.$store.getters.goods;
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
          href:
            "/" +
            pathArray[1] +
            "/" +
            pathArray[2] +
            "/" +
            pathArray[3] +
            "/" +
            pathArray[4]
        },
        {
          text: this.$store.getters.model,
          disabled: false,
          href: ""
        }
      ];
    },
    listGoods($) {
      let list = this.$store.getters.goodsList;
      if (this.$store.getters.debug) {
        console.log("comp listGoods");
        console.log(list);
      }

      if (!list.goods_name_list || list.goods_name_list.length == 0) {
        return;
      }

      let groups = {};
      list.goods_name_list.forEach(el => {
        //console.log(el);
        if (groups[el.group_name] == undefined) {
          groups[el.group_name] = {
            id: el.group_id,
            name: el.group_name,
            avail: 0,
            children: []
          };
        }
        if (el.goods_sh_avail > 0) {
          groups[el.group_name].children.push({
            id: el.goods_name_id,
            name: el.goods_name_short_ru + " - " + el.goods_sh_avail
            //to: "/url"
          });
          groups[el.group_name].avail += el.goods_sh_avail;
        }
      });

      if (this.$store.getters.debug) {
        console.log("groups:");
        console.log(groups);
      }

      let goods = [];
      for (var el in groups) {
        if (groups[el].children.length > 0) {
          goods.push({
            id: groups[el].id,
            name: groups[el].name + " - " + groups[el].avail,
            children: groups[el].children
          });
        }
      }

      if (this.$store.getters.debug) {
        console.log("goods:");
        console.log(goods);
      }

      return goods;
    }
  },
  mounted() {
    if (this.$store.getters.debug) {
      console.log("Goods");
      //console.log(pathArray);
    }

    const pathArray = this.$route.path.split("/");
    this.$store.dispatch("setTypeAutos", pathArray[2]);
    this.$store.dispatch("setFirm", pathArray[3]);
    this.$store.dispatch("setModelGroup", pathArray[4]);
    this.$store.dispatch("setModel", pathArray[5]);
    //this.$store.dispatch("fillGoods");
  }
};
</script>
