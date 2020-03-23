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
  getFirms(cb) {
    axios.get('/firm')
      .then(response => {
        if (debug) {
          console.log('api getFirms');
          console.log(response.data);
        }

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
  getModelGroups(firm, typeAutos, cb) {
    axios.get('/models/' + firm)
      .then(response => {
        if (debug) {
          console.log('api getModelGroups');
          console.log(response.data);
        }

        cb(response.data);
        //this.fillFromData(response.data);
      })
      .catch(error => {
        console.log("error " + error.response);
      })
  },
  getModel(modelUrl, cb) {
    axios.get('/model/' + modelUrl)
      .then(response => {
        if (debug) {
          console.log('api getModel');
          console.log(response.data);
        }

        cb(response.data);
        //this.fillFromData(response.data);
      })
      .catch(error => {
        console.log("error " + error.response);
      })
  },
  getModels(typeAutos, firm, modelGroup, cb) {
    axios.get('/' + typeAutos + '/' + firm + '/' + modelGroup)
      .then(response => {
        if (debug) {
          console.log('api getModels');
          //console.log(modelGroup);
          console.log(response.data);
        }

        cb(response.data);
        //this.fillFromData(response.data);
      })
      .catch(error => {
        console.log(error);
        cb({
          models: [],
          avail: [],
          error: error
        });
      })
  },
  getGoods(modelId, cb) {
    console.log("api getGoods_" + modelId);
    if (!modelId) {
      return [];
    }

    axios.get('/goods/' + modelId)
      .then(response => {
        if (debug) {
          console.log('api getGoods');
          console.log(response.data);
        }

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