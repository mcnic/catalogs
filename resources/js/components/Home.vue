<template>
  <v-container>
    <router-link :to="'/' + amtelUrl">{{ amtelTile }}</router-link>
  </v-container>
</template>

<script>
export default {
  data: () => ({
    amtelUrl: process.env.MIX_AMTEL_PREFIX,
    amtelTile: process.env.MIX_AMTEL_NAME
  }),
  mounted() {
    console.log("Component Home mounted.");
  }
};
</script>
