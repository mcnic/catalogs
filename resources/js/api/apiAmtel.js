/**
 * Mocking client-server processing
 */

import axios from 'axios';

const debug = process.env.MIX_NODE_ENV !== 'production'

const timeout = debug ? 100 : 0

const prefix = "/" + process.env.MIX_AMTEL_PREFIX
const listLightCars = []
const listTrucks = []
const firms = {}

export default {
  /*  getFirms(cb) {
      setTimeout(() => cb({
        lightCars: listLightCars.map(item => ({
          title: item.title,
          url: prefix + "/cars/" + item.id
        })),
        trucks: listTrucks.map(item => ({
          title: item.title,
          url: prefix + "/truck/" + item.id
        }))
      }), timeout)
    },*/
  getFirms(cb) {
    debug ? console.log('api getFirms') : '';

    axios.get('/firm')
      .then(response => {
        console.log(response.data);

        cb(response.data);
        //this.fillFromData(response.data);
        //this.$store.dispatch("setLoading", false);
      })
      .catch(error => {
        console.log("error " + error.response);
        //this.$store.dispatch("setLoading", false);
      })

    const autos = {
      lightCars: listLightCars.map(item => ({
        title: item.title,
        url: prefix + "/cars/" + item.id
      })),
      trucks: listTrucks.map(item => ({
        title: item.title,
        url: prefix + "/truck/" + item.id
      }))
    }

    /*setTimeout(() =>
      cb(autos)
      , timeout)*/
  },

  getFirm(cb, id) {
    debug ? console.log('api getFirm') : '';

    const firm = firms[id];

    setTimeout(() =>
      cb(firm)
      , timeout)
  },

  getModels(firm, typeAutos, cb) {
    if (debug) {
      console.log('api getModels');
      console.log(firm);
    }

    axios.get('/' + typeAutos + '/' + firm)
      .then(response => {
        console.log(response.data);

        cb(response.data);
        //this.fillFromData(response.data);
      })
      .catch(error => {
        console.log("error " + error.response);
      })

  },

  buyProducts(products, cb, errorCb) {
    setTimeout(() => {
      // simulate random checkout failure.
      (Math.random() > 0.5 || navigator.userAgent.indexOf('PhantomJS') > -1)
        ? cb()
        : errorCb()
    }, 100)
  }
}