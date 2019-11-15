/**
 * Mocking client-server processing
 */

const debug = process.env.MIX_NODE_ENV !== 'production'

const timeout = debug ? 100 : 0

const prefix = "/" + process.env.MIX_AMTEL_PREFIX
const listLightCars = [
  { id: 'acura', title: 'Acura' },
  { id: 'ford', title: 'Ford' },
  { id: 'nissan', title: 'Nissan' },
]
const listTrucks = [
  { id: 'baw', title: 'BAW' },
  { id: 'bpw', title: 'BPW' },
]
const firms = {
  'acura': 'Acura',
  'ford': 'Ford',
  'nissan': 'Nissan'
}

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
    debug ? console.log('getFirms') : '';

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

    setTimeout(() =>
      cb(autos)
      , timeout)
  },

  getFirm(cb, id) {
    debug ? console.log('getFirm') : '';

    const firm = firms[id];

    setTimeout(() =>
      cb(firm)
      , timeout)
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