<template>
  <v-app>
    <!-- top navigation bar -->
    <v-app-bar app color="indigo" dark>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
      <v-toolbar-title>{{ appName }}</v-toolbar-title>

      <v-spacer></v-spacer>

      <v-btn icon>
        <v-icon>mdi-magnify</v-icon>
      </v-btn>

      <v-btn icon>
        <v-icon>mdi-heart</v-icon>
      </v-btn>

      <v-menu bottom left>
        <template v-slot:activator="{ on }">
          <v-btn icon color="yellow" v-on="on">
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>

        <v-list>
          <v-list-item v-for="(item, i) in items" :key="i">
            <v-list-item-title>{{ item.title }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>

    <!-- left drawer -->
    <v-navigation-drawer app v-model="drawer" temporary>
      <v-list-item @click="gotoMainSite">
        <v-list-item-icon>
          <v-icon></v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>Вернуться в магазин</v-list-item-title>
        </v-list-item-content>
      </v-list-item>

      <v-list-item to="/">
        <v-list-item-icon>
          <v-icon>mdi-home-city</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>Главная страница</v-list-item-title>
        </v-list-item-content>
      </v-list-item>

      <v-divider></v-divider>

      <v-list dense>
        <v-list-item v-for="item in links" :key="item.title" :to="item.url">
          <v-list-item-icon>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title>{{ item.title }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <!-- content -->
    <v-content>
      <v-container fluid fill-height>
        <v-layout align-center justify-center>
          <router-view></router-view>
        </v-layout>
      </v-container>
    </v-content>

    <!-- footer -->
    <v-footer padless app>
      <v-col class="text-center" cols="12">
        {{ new Date().getFullYear() }} —
        <strong>{{ appName }}</strong>
      </v-col>
    </v-footer>
  </v-app>
</template>

<script>
export default {
  data: () => ({
    appName: "Каталог БУ запчастей",
    drawer: false,
    items: [
      { title: "Click Me" },
      { title: "Click Me" },
      { title: "Click Me" },
      { title: "Click Me 2" }
    ]
  }),
  methods: {
    gotoMainSite() {
      location.replace("http://www.autoimport31.ru");
    }
  },
  computed: {
    error() {
      //return this.$store.getters.error
    },
    linksTop() {
      return [
        {
          title: "Вернуть на сайт",
          icon: "",
          url: "http://www.autoimport31.ru"
        },
        { title: " Главная страница", icon: "mdi-home-city", url: "/" }
      ];
    },
    links() {
      if (this.isUserLoggedIn) {
        return [
          { title: "example", icon: "", url: "/e" }
          //{ title: "Прайсы", icon: "", url: "/loaders" },
          //{ title: "Кроссы", icon: "", url: "/crosses" },
          //{ title: "Производители", icon: "", url: "/brands" },
          //{ title: "Профили", icon: "", url: "/profiles" },
          //{ title: "XML", icon: "", url: "/wheels" },
          //{ title: "Выйти", icon: "lock", url: "/logout" }
        ];
      }

      return [
        { title: "Зайти", icon: "lock", url: "/login" }
        //{ title: "Зарегистрироваться", icon: "face", url: "/reg" }
      ];
    },
    isUserLoggedIn() {
      return true;
    }
  },
  mounted() {
    //console.log("Component App mounted.");
  }
};
</script>
