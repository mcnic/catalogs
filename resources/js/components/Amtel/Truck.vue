<template>
  <v-container>
    <v-breadcrumbs :items="bread" divider=">"></v-breadcrumbs>

    <v-list-item>
      <v-list-item-content>
        <v-list-item-title class="headline">Запчасти для {{ firm }}</v-list-item-title>
      </v-list-item-content>
    </v-list-item>
  </v-container>
</template>

<script>
export default {
  data: () => ({
    firm: "",
    bread: [
      {
        text: "Главная",
        disabled: false,
        href: "/"
      },
      {
        text: process.env.MIX_AMTEL_NAME,
        disabled: false,
        href: "/" + process.env.MIX_AMTEL_PREFIX
      }
    ]
  }),
  mounted() {
    this.firm = this.$route.params.id;
    this.bread.push({
      text: this.firm,
      disabled: true,
      href: "/" + process.env.MIX_AMTEL_PREFIX + "/" + this.firm
    });
    console.log("Component Amtel_Truck mounted.");
  }
};
</script>
