<template>
  <div class="col-md-8 mx-auto pb-2">
    <div class="card card-primary">
      <form>
        <div class="card-body">
          <div class="form-group">
            <label>First Name</label>
            <input class="form-control" :disabled="!admin_mod_id" :value="user.firstname" type="text" @change="submit($refs.firstname.value)" @keyup.enter="submit($refs.firstname.value)" ref="firstname">
          </div>
          <div class="form-group">
            <label>Last Name</label>
            <input class="form-control" :disabled="!admin_mod_id" :value="user.lastname" type="text" @change="submit($refs.lastname.value)" @keyup.enter="submit($refs.lastname.value)" ref="lastname">
          </div>
          <div class="form-group">
            <label>Username</label>
            <input class="form-control" :disabled="!admin_mod_id" :value="user.username" type="text" disabled>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input class="form-control" :disabled="!admin_mod_id" :value="user.email" type="email" @change="submit($refs.email.value)" @keyup.enter="submit($refs.email.value)" ref="email">
          </div>
          <div class="form-group">
            <label>Role</label>
            <select class="form-control" :disabled="!admin_mod" @change="submit($refs.roles.value)" @keyup.enter="submit($refs.roles.value)" ref="roles">
              <template v-for="role in roles">
                <template v-for="userrole in userroles">

                  <option :selected="userrole.name==role.name ? 'selected':''" :value="role.name">{{role.name}}</option>

                </template>
              </template>
            </select>
          </div>
          <div class="form-group">
            <label for="facebook">Facebook</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">https://facebook.com/</span>
              </div>
              <input type="text" :disabled="!admin_mod_id" class="form-control" :value="user.facebook" ref="facebook" @change="submit($refs.facebook.value)" @keyup.enter="submit($refs.facebook.value)" autofocus>
            </div>
          </div>

          <div class="form-group">
            <label for="twitter">Twitter</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">https://twitter.com/</span>
              </div>
              <input type="text" :disabled="!admin_mod_id" class="form-control" :value="user.twitter" ref="twitter" @change="submit($refs.twitter.value)" @keyup.enter="submit($refs.twitter.value)" autofocus>
            </div>
          </div>
          <div class="form-group">
            <label for="photo">Change Photo</label>
            <div class="photo"><img :src="user.photo ? user.photo :'/images/avatar.jpg'" alt=""></div>
            <div class="input-group">
              <input class="form-control" :disabled="!admin_mod_id" name="photo" @change="photo($refs.photo.files[0])" type="file" accept="image/*" ref="photo">
            </div>
          </div>
          <div v-if="auth_id">
          <div class="form-group">
            <label>Old Password</label>
            <input class="form-control" :disabled="!admin_mod_id" type="password" @change="pass('new')" ref="oldpass">
          </div>
          <div class="form-group">
            <label>New Password</label>
            <input class="form-control" :disabled="!admin_mod_id" type="password" @change="pass('confirm')" ref="newpass">
          </div>
          <div class="form-group">
            <label>Comfirm Password</label>
            <input class="form-control" :disabled="!admin_mod_id" type="password" @change="newpass($refs.oldpass.value, $refs.newpass.value, $refs.conpass.value)" @keyup.enter="newpass($refs.oldpass.value, $refs.newpass.value, $refs.conpass.value)" ref="conpass">
          </div>
        </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      user:"",
      userroles:"",
      roles:"",
      admin_mod:"",
      admin_mod_id:"",
      auth_id:"",
    }
  },
  props:[
  'id',
  ],
  methods:{
    err(error){
      if (error.response.data.errors) {
        Object.keys(error.response.data.errors).forEach( function(key) {
    // statements
    Object.keys(error.response.data.errors[key]).forEach( function(e) {
      this.danger(error.response.data.errors[key][e]);
    }, this);
  }, this);
      }
    },
    newpass(old, newpass, conpass){
      
      swal({
        title: "Are you sure?",
        text: "Please confirm to change your password",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willChange) => {
        if (willChange) {
          loader();
          axios.put('/api/user/'+this.id, {
            oldpass:old,
            password:newpass,
            password_confirmation:conpass,
          })
          .then(response => {
            swal.close();
  //console.log(response.data);
  if (response.data.fail) {
    this.danger(response.data.fail)
  }else {
    this.users = response.data;
    this.success('password changed successfullly')
  }
  
})
          .catch((error)=>{
            swal.close();
            console.log(error.response.data);
            this.err(error);

          })
        }
      });
    },
    pass(data){
      this.success('You can now type your '+data+ ' password')
    },
    photo(value){
      //console.log(value);
    //this.user.photo = '';
    var formData = new FormData();
    formData.append('photo', value);
    formData.append('_method', 'PATCH');
    //console.log(options);
    this.user.photo = '/images/avatar.jpg';
    this.loader();
    axios.post('/api/user/'+this.id, formData)
    .then(response => {
      swal.close();
      console.log(response.data);
      this.user = response.data.user;
  //this.user.photo = '/images/avatar.jpg';
  //this.user.photo = response.data.user.photo;
  if (response.data.fail) {
    this.danger(response.data.fail)
  }else {
    this.success('Photo changed successfullly')
  }

})
    .catch((error)=>{
      swal.close();
      console.log(error.response.data);
      this.err(error);
    })
  },
  submit(value){
      //console.log(this.$refs.lastname.value);
      var Data ={};

      if (this.$refs.firstname.value == value) {
        var data = "firstname";
        Data.firstname = value;
      }
      if (this.$refs.lastname.value == value) {
        var data = "lastname";
        Data.lastname = value;
      }
      if (this.$refs.email.value == value) {
        var data = "email";
        Data.email = value;
      }
      if (this.$refs.roles.value == value) {
        var data = "role";
        Data.role = value;
      }
      if (this.$refs.facebook.value == value) {
        var data = "facebook";
        Data.facebook = value;
      }
      if (this.$refs.twitter.value == value) {
        var data = "twitter";
        Data.twitter = value;
      }
  //console.log(Data);
  loader();
  axios.put('/api/user/'+this.id, Data)
  .then(response => {
    swal.close();
  //console.log(response.data.name);
  if (response.data.fail) {
    this.danger(response.data.fail)
  }else {
    //this.users = response.data;
    this.success(data+' changed successfullly')
  }
  
})
  .catch((error)=>{
    swal.close();
    console.log(error.response.data);
    this.err(error);
  })
},
danger:function(error){
  this.$notify.danger(error,{
    iconClass: 'fa fa-exclamation-triangle',
    visibility:5000,
    permanent:false,
  });
},
success:function(msg){
  this.$notify.success(msg,{
    iconClass: 'fa fa-check',
    visibility:5000,
  });
},

},
created(){
  //console.log(axios.defaults.headers.common['Authorization']);
  loader();
  swalclose();
  //var id = window.location.pathname.split('/').pop();
  //console.log(this.id);
  axios.get('/api/user/'+this.id+'/edit')
  .then(response => {
    swal.close();
    console.log(response);
    if (response.data.fail) {
      swal(response.data.fail, {
        icon: "error",
      });
      window.location.href = "/al-admin/user/";
      return;
    }
    this.id = response.data.user.id;
    this.roles = response.data.roles;
    this.userroles = response.data.user.roles;
    this.user = response.data.user;
    this.admin_mod = response.data.admin_mod;
    this.admin_mod_id = response.data.admin_mod_id;
    this.auth_id = response.data.auth_id;
    
    
  })
  .catch((error)=>{
    swal.close();
    console.log(error.response.data);
    this.err(error);
    
  })
}
}



</script>

<style>

</style>
