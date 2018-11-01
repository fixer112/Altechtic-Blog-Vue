
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
const token = localStorage.getItem('token');
if (token) {
  axios.defaults.headers.common['Authorization'] = 'Bearer '+token;
}

window.Vue = require('vue');
import Notify from 'vue2-notify'
import vSelect from 'vue-select'
//import VModal from 'vue-js-modal'

Vue.use(Notify);
window.danger = function(error){
  $notify.danger(error,{
    iconClass: 'fa fa-exclamation-triangle',
    visibility:5000,
    permanent:true,
  });
};
window.success = function(msg){
  $notify.success(msg,{
    iconClass: 'fa fa-check',
    visibility:5000,
  });
};
//Vue.use(VModal);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));
//Vue.component('bootstrap-modal', require('vue2-bootstrap-modal'));
Vue.component('register', require('./components/Auth/Register.vue'));
Vue.component('login', require('./components/Auth/Login.vue'));
Vue.component('logout', require('./components/Auth/Logout.vue'));
Vue.component('setting', require('./components/Admin/Settings.vue'));
Vue.component('alluser', require('./components/Admin/user/Index.vue'));
Vue.component('edituser', require('./components/Admin/user/Edit.vue'));
Vue.component('allpost', require('./components/Admin/post/Index.vue'));
Vue.component('newpost', require('./components/Admin/post/New.vue'));
Vue.component('editpost', require('./components/Admin/post/Edit.vue'));

Vue.component('posts', require('./components/Posts.vue'));
Vue.component('post', require('./components/Post.vue'));
Vue.component('sidebar', require('./components/Sidebar.vue'));
Vue.component('page-footer', require('./components/Footer.vue'));
Vue.component('v-select', vSelect);

const app = new Vue({
    el: '#app',
    created(){
    	//console.log(localStorage.getItem('token'))
    if (window.location.pathname=='/login') {
    	localStorage.removeItem('token')
    	//console.log('path here')
    }
  },
  methods:{
  	
  }
});
