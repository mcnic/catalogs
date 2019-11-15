<template>
  <v-card
    class="d-inline-block mx-auto model-tile col-xs-6 col-sm-6 col-md-4 col-lg-4"
    max-width="250"
    hover
    :href="$attrs.url"
    padding="10"
    style="margin=10"
  >
    <v-card-title>{{ $attrs.id }}</v-card-title>

    <v-container>
      <v-row justify="space-between">
        <v-col cols="auto">
          <v-img max-width="128" :src="$attrs.image"></v-img>
        </v-col>

        <v-col cols="auto" class="text-center pl-0">{{ $attrs.text }}</v-col>
      </v-row>
    </v-container>
  </v-card>
</template>

<script>
export default {
  data: () => ({
    props: {
      id: String,
      url: String,
      image: String,
      text: String
    }
  }),
  mounted() {
    //this.$store.getters.debug ? console.log("autoInfo") : "";
  }
};
</script>
