<template>
  <div class="col-md-12 pb-2">
    <div class="card card-primary">
      <div class="card-body">
        <div class="table-responsive m-t-40">
          <table id="due" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Roles</th>
                <th>Date Joined</th>
                <th>Actions</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="user in users" :key="user.id">
                <td>{{user.id}}</td>
                <td>{{user.firstname}}</td>
                <td>{{user.lastname}}</td>
                <td>{{user.email}}</td>
                <td><p v-for="roles in user.roles">{{roles.name}}</p></td>
                <td>{{user.created_at}}</td>
                <td>
                  <button v-on:click="edit(user.id, user.firstname)" class="btn btn-warning"><i class="fa fa-edit"></i> Edit</button>
                  <button v-on:click="remove(user.id, user.firstname)" class="btn btn-danger"><i class="fa fa-trash"></i> Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      users:[],
    }
  },
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
    edit(id, name){
      loader();
      axios.get('/api/user/'+id+'/edit')
      .then(response => {
        swal.close();
        console.log(response.data);
        if (response.data.fail) {
          swal(response.data.fail, {
            icon: "error",
          });
        }else {
          /*this.id = response.data.user.id;
          this.roles = response.data.roles;
          this.userroles = response.data.user.roles;
          this.user = response.data.user;*/
          window.location.href = "/al-admin/user/"+id+'/edit';
        }
        
      })
      .catch((error)=>{
        swal.close();
        console.log(error.response.data);
        this.err(error);
        
      })
    },
    remove(id, name){
      swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover "+name,
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
          loader();
          axios.delete('/api/user/'+id)
          .then(response => {
            swal.close();
            //console.log(response.data);
            if (response.data.fail) {
              swal(response.data.fail, {
                icon: "error",
              });
            }else {
              this.users = response.data;
              $('#due').DataTable().destroy();
              swal(name+' deleted successfully', {
                icon: "success",
              });
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
    //console.log(axios.defaults.headers.common);
    loader();
    swalclose();
    axios.get('/api/user')
    .then(response => {
      swal.close();
      console.log(response.data);
      this.users = response.data.users;
    })
    .catch((error)=>{
      swal.close();
      console.log(error.response.data);
      this.err(error);
      
    })
  },
  updated(){
    $('#due').DataTable().destroy();
    $('#due').DataTable({
      "order": [
      [0, 'desc']
      ]

    });
  },
  mounted(){
    //console.log(localStorage.getItem('user'));
  }
}



</script>

<style>

</style>
