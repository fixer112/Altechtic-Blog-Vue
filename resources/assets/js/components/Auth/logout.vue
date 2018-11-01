<template>
  
  <a v-bind:href="url" v-on:click="destroy" class="nav-link">
    <i class="nav-icon fa fa-sign-out"></i>
    <p>
     Logout
   </p>
 </a>
 
</template>

<script>
export default {
  data () {
    return {
      url:"/logout/?token="+localStorage.getItem('token')
    }
  },
  methods:{
    destroy:function(){
      localStorage.removeItem('token')
    }
  },
}



</script>

<style>

</style>
