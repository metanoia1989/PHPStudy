require('./bootstrap');
const Vue = require('vue');
const vueBaberrage = require('vue-baberrage').vueBaberrage;
window.Vue = Vue;
Vue.use(vueBaberrage);
const DanmuComponent = require('./components/DanmuComponent.vue').default;
window.Danmu = DanmuComponent;
Vue.component('danmu-component', DanmuComponent);

const app = new Vue({
    el: '#app',
});
