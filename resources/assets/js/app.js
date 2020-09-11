
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import Swal from 'sweetalert2';
window.Swal = Swal;
window.Vue = require('vue');


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('cargo', require('./components/Cargo.vue'));
Vue.component('conf_causas', require('./components/ConfCausas.vue'));
Vue.component('conf_indicadores', require('./components/ConfIndicadores.vue'));
Vue.component('conf_proyectos', require('./components/ConfProyectos.vue'));
Vue.component('configgenerales', require('./components/ConfigGenerales.vue'));
Vue.component('cuadro_mando', require('./components/CuadroMando.vue'));
Vue.component('gest_actividades', require('./components/GestActividades.vue'));
Vue.component('modulo', require('./components/Modulo.vue'));
Vue.component('pendientes', require('./components/Pendientes.vue'));
Vue.component('plan_accion', require('./components/PlanAccion.vue'));
Vue.component('raic', require('./components/Raic.vue'));
Vue.component('rol', require('./components/Rol.vue'));
Vue.component('segmento', require('./components/Segmento.vue'));
Vue.component('sistema', require('./components/Sistema.vue'));
Vue.component('solicitud', require('./components/Solicitud.vue')); 
Vue.component('terceros', require('./components/Terceros.vue'));
Vue.component('user', require('./components/User.vue'));
const app = new Vue({
    el: '#app',
    data :{
        menu : 0,
        ruta : 'http://localhost/laravel_estudio/sgi/public',
        permisosUser : [],
        axiosApp : axios.create({baseURL : 'http://localhost/laravel_estudio/sgi/public'})
    },

    mounted() {
        let me = this;
        var url= this.ruta +'/permisos/listarPermisosLogueado';
        axios.get(url).then(function (response) {
            me.permisosUser = response.data.permisosLogueado;
        })
        .catch(function (error) {
            console.log(error);
        });
    }
});