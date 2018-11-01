<template>
  <div>
    <form v-on:submit.prevent="submit">
      <div class="form-group">
        <label for="email">Username</label>

        <input id="username" type="text" class="form-control" name="username" v-model="username"  required autofocus>
      </div>

      <div class="form-group">
        <label for="password">Password
          <a href="forgot.html" class="float-right">
            Forgot Password?
          </a>
        </label>
        <input id="password" type="password" class="form-control" name="password" v-model="password" required data-eye>
      </div>
      
      <div class="form-group no-margin">
        <button type="submit" class="btn btn-primary btn-block">
          Login
        </button>
      </div>
      <div class="margin-top20 text-center">
        Don't have an account? <a href="register">Create One</a>
      </div>
    </form>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data () {
    return {
      username:"",
      password:"",
      remember:false,
    }
  },
  methods:{
    danger:function(error){
      this.$notify.danger(error,{
        iconClass: 'fa fa-exclamation-triangle',
        visibility:20000,
      });
    },
    success:function(msg){
      this.$notify.success(msg,{
        iconClass: 'fa fa-check',
        visibility:20000,
      });
    },
    submit:function(){
      var span = document.createElement("p");
      span.classList.add("loader");
      swal({
        title:"",
        content:span,
        buttons:false,
        className: "ajax",
        closeOnClickOutside: false,
        closeOnEsc: false,
      })
      axios.post('api/login', {
        password:this.password,
        username:this.username,
        remember:this.remember,
      }).then(response => {
        swal.close();
        console.log(response.data);
        if (response.data.fail) {
          this.danger(response.data.fail);
        }
        if (response.data.access_token) {
          this.success("Login successfull");
          localStorage.setItem('token', response.data.access_token);
          window.location.href = "al-admin"; 

    }

  })
      .catch((error)=>{
        swal.close();
        console.log(error.response.data);
        if (error.response.data.errors) {
          Object.keys(error.response.data.errors).forEach( function(key) {
    // statements
    Object.keys(error.response.data.errors[key]).forEach( function(e) {
      this.danger(error.response.data.errors[key][e]);
    }, this);
  }, this);
        }
      })
    
      
    },
  },
  created(){
    console.log(axios.defaults.headers.common);
    
  },
  mounted(){
  }
}
</script>

<style>

</style>
