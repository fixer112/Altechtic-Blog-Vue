<template>
  <div class="col-md-12 pb-2">
    <div class="card card-primary">
      
      <div class="card-body">
        <div class="mb-2">
          <button class="btn btn-primary" :disabled="type=='all'" @click="fpost('all')">All</button>
          <button class="btn btn-primary" :disabled="type==1" @click="fpost('1')">Publish</button>
          <button class="btn btn-primary" :disabled="type==0" @click="fpost('0')">Draft</button>
          <button class="btn btn-primary" :disabled="type=='trash'" @click="fpost('trash')">Trash</button>
        </div>
        <div class="mb-2" v-if="can_delete || can_publish">
          <div class="mb-2" v-if="type!='trash' && can_publish">
            <div class="mb-2">
              <button class="btn btn-primary" @click="s_all">Select All</button>
            </div>
            <select class="form-control col-md-2" ref="all">
              <option value="1">Publish</option>
              <option value="0">Draft</option>
              <option value="trash">Trash</option>
            </select>
            
            <button class="btn btn-primary mt-2" @click="u_all($refs.all.value)">Submit</button>
            
          </div>
          <div class="mb-2" v-if="type=='trash' && can_delete">
            <div class="mb-2">
              <button class="btn btn-primary" @click="s_all">Select All</button>
            </div>
            <select class="form-control col-md-2" ref="all">
              <option value="delete">Delete</option>
            </select>
            
            <button class="btn btn btn-primary mt-2" @click="d_all($refs.all.value)">Submit</button>
            
          </div>
        </div>
        <div class="table-responsive m-t-40 mt-2">
          <table id="due" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Status</th>
                <th>Author</th>
                <th>Categories</th>
                <th>Date Updated</th>
                <th>Actions</th>
              </tr>
            </thead>
            
            <tbody>
              <tr @click="select(post.id)" v-for="post in posts" :key="post.id" v-if="can_edit || can_publish || post.user.id==auth_id">
                <td>{{post.id}}</td>
                <td>{{post.title}}</td>
                <td v-if="type!='trash'">{{post.status=='1'? 'Publish':'Draft'}}</td>
                <td v-if="type=='trash'">Trash</td>
                <td>{{post.user.username}}</td>
                <td><i v-for="category in post.categories" class="cat">{{category.name}}</i></td>
                <td>{{post.updated_at}}</td>
                <td>
                  <span v-if="type!='trash'">
                    <button v-on:click="edit(post.id)" class="btn btn-warning"><i class="fa fa-edit"></i> Edit</button>

                    <button v-on:click="update(post.id, post.status=='0'? '1':'0')"  v-if="can_publish" class="btn btn-primary"><i :class="post.status=='1'? 'fa fa-paperclip':'fa fa-save'"></i> {{post.status=='1'? 'Draft':'Publish'}}</button>

                    <button v-on:click="update(post.id,'trash')"  v-if="can_publish || post.user.id==auth_id" class="btn btn-danger"><i class="fa fa-trash"></i> Trash</button>
                  </span>
                  <span v-if="type=='trash'">
                    <button v-on:click="update(post.id,post.status)"  v-if="can_publish || post.user.id==auth_id" class="btn btn-success"><i class="fa fa-window-restore"></i> Restore</button>
                    <button v-on:click="pdelete(post.id, 'trash')"  v-if="can_delete || post.user.id==auth_id" class="btn btn-danger"><i class="fa fa-trash"></i> Delete</button>
                  </span>
                  <span>
                    <i v-for="id in ids" v-if="id==post.id" class="fa fa-check check"></i>
                  </span>
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
      posts:[],
      type:"all",
      can_edit:"",
      can_publish:"",
      can_delete:"",
      auth_id:"",
      ids:[],
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
    edit(id){
      loader();
      axios.get('/api/post/'+id+'/edit')
      .then(response => {
        swal.close();
        console.log(response.data);
        if (response.data.fail) {
          swal(response.data.fail, {
            icon: "error",
          });
        }else {
          window.location.href = "/al-admin/post/"+id+'/edit';
        }
        
      })
      .catch((error)=>{
        swal.close();
        console.log(error.response.data);
        this.err(error);
        
      })
    },
    update(id, status, refresh = 'yes'){
      loader();
      axios.put('/api/post/'+id,{status:status})
      .then(response => {
        swal.close();
        console.log(response.data.posts);
        if (response.data.fail) {
          swal(response.data.fail, {
            icon: "error",
          });
          return;
        }
    //this.posts = response.data.posts;
    if (refresh == 'yes') {
      this.success('Post updated successfullly');
      this.fpost(status);
    }
  //$('#due').DataTable().destroy();
})
      .catch((error)=>{
        swal.close();
        console.log(error.response.data);
        this.err(error);
        
      })
    },
    pdelete(id, status){
      swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover post",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
          loader();
          axios.delete('/api/post/'+id)
          .then(response => {
            swal.close();
            console.log(response.data);
            if (response.data.fail) {
              swal(response.data.fail, {
                icon: "error",
              });
              return;
            }
    //this.posts = response.data.posts;
    this.success('Post deleted successfullly');
    this.fpost(status);
    
  //$('#due').DataTable().destroy();
})
          .catch((error)=>{
            swal.close();
            console.log(error.response.data);
            this.err(error);
            
          })
        }
      });
    },
    d_all(status){
      if (this.ids.length == 0) {
        this.danger('No post selected');
        return;
      }

      swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover posts",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
          var ids = this.ids;
          for(let id in ids){
        //console.log(this.ids[id]);
        loader();
        axios.delete('/api/post/'+ids[id])
        .then(response => {
          swal.close();
          console.log(response.data);
          if (response.data.fail) {
            swal(response.data.fail, {
              icon: "error",
            });
            return;
          }
        })
        .catch((error)=>{
          swal.close();
          console.log(error.response.data);
          this.err(error);
          
        })
      }
      this.success('Posts deleted successfullly');
      this.fpost('trash');
      /*this.ids = [];*/
    }
  });
/*this.success('Posts deleted successfullly');
this.fpost('trash');*/
},
u_all(status){
  if (this.ids.length == 0) {
    this.danger('No post selected');
    return;
  }
  var ids = this.ids;
  for(let id in ids){
        //console.log(this.ids[id]);
        this.update(ids[id], status, 'no');
      }
      this.success('Posts updated successfullly');
      this.fpost(status);
      /*this.ids = [];*/
    },
    select(id){
      if (this.ids.includes(id)) {
        var index = this.ids.indexOf(id);
        this.ids.splice(index, 1);
        return;
      }
      this.ids.push(id);
    },
    s_all(){
      this.ids = [];
      var a = $('#due').DataTable().columns(0,{page:'current'})
      .data()
      .eq(0);
      //console.log(a[0]);
      Object.keys(a).forEach( function(key) {
        //console.log(key);
        var reg = new RegExp('^[0-9]+$');
        if (reg.test(key)) {
          //console.log(a[key]);
          this.ids.push(parseInt(a[key]));
          //this.ids[key] = a[key];
        }
      }, this);
      //this.ids = ne;
      //console.log(this.ids);
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
    fpost(data='all'){
      this.type = data;
      loader();
      swalclose();

      axios.get('/api/post/?type='+data)
      .then(response => {
        swal.close();
        console.log(response.data);
        this.posts = response.data.posts;
        this.can_edit = response.data.can_edit;
        this.can_publish = response.data.can_publish;
        this.can_delete = response.data.can_delete;
        this.auth_id = response.data.auth_id;
        $('#due').DataTable().destroy();
        this.ids = [];
      })
      .catch((error)=>{
        swal.close();
        console.log(error.response.data);
        this.err(error);
        
      })

    },

  },
  created(){
    this.fpost(this.type);
    swalclose();
  },
  updated(){
    $('#due').DataTable().destroy();
    $('#due').DataTable({
      "order": [
      [5, 'desc']
      ]

    });
  },
  mounted(){
  }
}



</script>

<style>

</style>
