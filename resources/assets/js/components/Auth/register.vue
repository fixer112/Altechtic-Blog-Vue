<template>
  <div>
    <form v-on:submit.prevent="submit">
     <div class="form-group">
      <label for="firstname"><span class="star">* </span>Firstname</label>

      <input id="firstname" type="text" class="form-control" name="firstname" v-model="firstname" required autofocus>
    </div>

    <div class="form-group">
      <label for="lastname"><span class="star">* </span>Lastname</label>

      <input id="lastname" type="text" class="form-control" name="lastname" v-model="lastname"  required autofocus>
    </div>

    <div class="form-group">
      <label for="lastname"><span class="star">* </span>Username</label>

      <input id="username" type="text" class="form-control" name="username" v-model="username"  required autofocus>
    </div>

    <div class="form-group">
      <label for="email"><span class="star">* </span>E-Mail Address</label>

      <input id="email" type="email" class="form-control" name="email" v-model="email" ref="email" required autofocus>
    </div>

    <div class="form-group">
      <label for="facebook">Facebook</label>
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text">https://facebook.com/</span>
        </div>
        <input id="facebook" type="text" class="form-control" name="facebook" v-model="facebook" autofocus>
      </div>
    </div>

    <div class="form-group">
      <label for="twitter">Twitter</label>
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text">https://twitter.com/</span>
        </div>
        <input id="twitter" type="text" class="form-control" name="twitter" v-model="twitter" autofocus>
      </div>
    </div>

    <div class="form-group">
      <label for="password"><span class="star">* </span>Password</label>
      <input id="password" type="password" class="form-control" name="password" v-model="password" required data-eye>
    </div>

    <div class="form-group">
      <label for="password"><span class="star">* </span>Confirm Password</label>
      <input id="passwordcom" type="password" class="form-control" name="password_confirmation" v-model="password_confirmation" required data-eye>
    </div>

    <div class="form-group">
      <label for="photo">Photo</label>
      <div class="input-group">
        <input class="form-control" name="photo" id="photo" type="file" accept="image/*" ref="file" v-on:change="handle">
      </div>
    </div>

    
    <div class="form-group no-margin">
      <button type="submit" class="btn btn-primary btn-block">
        Sign Up
      </button>
    </div>
    <div class="margin-top20 text-center">
      Already have an account? <a href="login">Login now</a>
    </div>
  </form>
</div>
</template>

<script>
export default {
  data () {
    return {
      welcome:false,
      firstname:"",
      lastname:"",
      username:"",
      email:"",
      password:"",
      password_confirmation:"",
      file:"",
      facebook:"",
      twitter:"",
      
    }
  },
  methods:{
    handle:function(){
      var fl= document.querySelector('#photo');
      console.log(fl.files[0]);
      //this.file = this.$refs.file.files[0];
      this.file = fl.files[0];
      console.log(this.$refs.file.files[0]);
    },
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
      let formData = new FormData();
      formData.set('firstname', this.firstname);
      formData.set('lastname', this.lastname);
      formData.set('email', this.email);
      formData.set('password', this.password);
      formData.set('password_confirmation', this.password_confirmation);
      formData.set('username', this.username);
      formData.set('photo', this.file);
      if (this.facebook != '') {
        formData.set('facebook', this.facebook);
      }
      if (this.twitter != '') {
        formData.set('twitter', this.twitter);
      }
      var span = document.createElement("p");
      span.classList.add("loader");
      //console.log(this.$refs.email.value);
      swal({
        title:"",
        content:span,
        buttons:false,
        className: "ajax",
        closeOnClickOutside: false,
        closeOnEsc: false,
      })
      axios.post('/api/register', formData/*,{
      headers:{
        'Content-Type':'multipart/form-data'
      }
    }*/)
      .then(response => {
        swal.close();
        console.log(response.data);
        this.success("Registration successful")
        //setTimeout(function () {
         window.location.href = "login"; 
       //}, 2000);

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

  },
  mounted(){/*
    let j = document.createElement('script');
    j.setAttribute('src',"http://127.0.0.1:8000/js/jquery.min.js");
    document.body.appendChild(j);

    let notify = document.createElement('script');
    notify.setAttribute('src',"js/sweetalert.min.js");
    document.body.appendChild(notify);*/
  }
}
</script>

<style>

</style>
