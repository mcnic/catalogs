<template>
  <v-row no-gutters style="flex-wrap: nowrap;">
    <v-col cols="2" class="flex-grow-1 flex-shrink-1">
      <v-card flat>
        <v-img height="88" width="116" :src="$attrs.img.length?$attrs.img[0].thumbnail_uri:''"></v-img>
      </v-card>
    </v-col>
    <v-col cols="6" class="flex-grow-1 flex-shrink-1">
      <v-card flat>
        Б/У, износ {{ $attrs.wearout }}%
        <br />
        {{ $attrs.num }} {{ $attrs.company }}
        <br />
        {{ $attrs.comment }}
      </v-card>
    </v-col>
    <v-col cols="2" class="flex-grow-1 flex-shrink-1">
      <!-- v-card flat>{{ $attrs.price }}руб.</v-card -->
    </v-col>
    <v-col cols="2" class="flex-grow-1 flex-shrink-1">
      <!--v-card
        flat
        link
        :to="mainPath + '&id=' + $attrs.num + '&brand=' + $attrs.company"
      >{{ $attrs.avail }}</v-card-->
      <a
        v-if="$attrs.num != '' & $attrs.company !=''"
        :href="mainPath + '&id=' + $attrs.num + '&brand=' + $attrs.company"
        target="_blank"
      >найти предложения</a>
    </v-col>
  </v-row>
</template>

<script>
export default {
  data: () => ({
    props: {
      id: String,
      comment: String,
      name: String,
      company: String,
      num: String,
      avail: BigInt,
      price: String,
      wearout: Float32Array,
      img: Array
    },
    mainPath: ""
  }),
  mounted() {
    //this.$store.getters.debug ? console.log("goodInfo") : "";
    this.mainPath = process.env.MIX_MAIN_URL + "/index.php?option=com_seek";
  }
};
</script>