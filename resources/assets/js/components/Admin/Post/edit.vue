<template>
  <div class="row pb-2">
    <div class="col-md-8">
      <div class="card card-primary">
        <form @submit.prevent>
          <div class="card-body">
            <div class="form-group">
              <label>Title</label>
              <input class="form-control" :disabled="!can_edit && !auth_id" v-model="post.title" @keyup="sluged">
            </div>
            <div class="form-group">
              <label>Slug</label>
              <input class="form-control" :disabled="true" v-model="post.slug" @change="slugg">
            </div>
            
            <div class="form-group">
              <textarea class="form-control" id="editor" v-ckeditor></textarea>
              count: {{post.body.length}}
              
              <button class="btn btn-primary float-right mt-2" @click="preview">Preview content</button>
              
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="col-md-4">

      <div class="card card-primary">
        <div class="mb-2 card-body">
          <div v-if="can_publish || auth_id">
            <button v-on:click="update(post.status=='0'? '1':'0')" v-if="can_publish" class="btn btn-primary"><i :class="post.status=='1'? 'fa fa-paperclip':'fa fa-save'"></i> {{post.status=='1'? 'Draft':'Publish'}}</button>
            <button v-if="post.trash=='0'" v-on:click="update('trash')" class="btn btn-danger"><i class="fa fa-trash"></i> Trash</button>
          </div>
          <div class="mt-2" v-if="can_edit || can_publish || auth_id">
            <button v-on:click="update(post.status)" class="btn btn-success"><i class="fa fa-refresh"></i> Update</button>
          </div>
        </div>
      </div>

      <div class="card card-primary">
        <div class="mb-2 mt-3 card-body">
            <!-- <div class="form-group">
                <label>Feature Image</label>
                <img :src="img" alt="">
                <input class="form-control" type="file" accept="image/*" ref="photo" @change="pic">
              </div> -->
              <label>Feature Image</label>
              <div class="input-group">
                <span class="input-group-btn">
                  <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
                    <i class="fa fa-picture-o"></i> Choose Image
                  </a>
                </span>
                <!-- <input id="thumbnail" class="form-control" type="text" name="filepath" @ ref="url"> -->
              </div>
              <div id="holder" style="margin-top:15px;"></div>
              <div>
                <button class="btn btn-danger mt-2" @click="remove_pic">Remove</button>
              </div>
            </div>
          </div>
          <div class="card card-primary">
            <div class="mb-2 mt-3 card-body">
              <label>Categories</label>
              <template v-for="category in categorys">
                <p>
                  {{category.name}}
                  <input type="checkbox" :disabled="!can_edit && !auth_id" :checked="check(category.id)" :value="category.id" @click="add_category(category.id)">
                </p>
              </template>
              {{category}}
            </div>
          </div>
        </div>
        
      </div>

    </template>

    <script>
    export default {
      data () {
        return {
          title:"",
          slug:"",
          body:"",
          post:[],
          can_publish:"",
          can_edit:"",
          categorys:[],
          category:[],
          auth_id:"",
        }
      },
      props:[
      'id',
      ],
      directives:{
        ckeditor:{
          inserted:function(el, binding, vnode){
            var token = document.head.querySelector('meta[name="csrf-token"]').content;
            var options={
              filebrowserImageBrowseUrl: '/uploads?type=Images',
              filebrowserImageUploadUrl: '/uploads/upload?type=Images&_token='+token,
              filebrowserBrowseUrl: '/uploads?type=Files',
              filebrowserUploadUrl: '/uploads/upload?type=Files&_token='+token,
            };

            var editor = CKEDITOR.replace('editor', options);
            editor.setData(vnode.context.post.body);
            editor.on('change', function(e){
              vnode.context.post.body = editor.getData();
        //console.log(editor.getData());
      });
            CKEDITOR.config.readOnly = !vnode.context.can_edit && !vnode.context.auth_id;
          }
        },
      },
      methods:{
        check(id){
          if (this.category.includes(id)) {
            return true;
          }
        },
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
        preview(){
          var p = document.createElement("p");
          p.classList.add("preview_body");
      //$(".l").html(this.body);
      //p.html(this.body);
      swal({
        title:"",
        content:p,
        buttons:true,
        className: "swal-preview",
        closeOnClickOutside: false,
        closeOnEsc: true,
      });
      $(".preview_body").html(this.post.body);
    },
    remove_pic(){
      var target_preview = $('#holder');
      target_preview.html('');
      url = '';
    },
    add_category(id){
     if (this.category.includes(id)) {
      var index = this.category.indexOf(id);
      this.category.splice(index, 1);
      return;
    }
    this.category.push(id);
  },
    /*pic(){
      this.photo = this.$refs.photo.files[0];
      if(this.photo){
        console.log(this.photo);
      }
    },*/
    slugg(){/*
      var nslug = this.slug.replace(/\s+/g,'-');
      this.slug = nslug.toLowerCase();*/
    },
    sluged(){
      var ntitle = this.post.title.replace(/\s+/g,' ');
      ntitle = ntitle.replace(/-+/,'-');
      this.post.title = ntitle;/*
      var nslug = ntitle.replace(/\s+/g,'-');
      nslug = nslug.replace(/-+/,'-');
      this.slug = nslug.toLowerCase();*/
    },
    update(status){
      let formData = new FormData();
      if (this.can_edit || this.auth_id) {
        formData.append('title', this.post.title);
        formData.append('slug', this.post.slug);
        formData.append('body', this.post.body);
        if (url!='') {
          formData.append('photo_url', url);
        }
        if (this.category.length>0) {
          formData.append('category', JSON.stringify(this.category));
        }
      }
      console.log(status);
      formData.append('status', status);
      formData.append('_method', 'PATCH');
      

      loader();
      axios.post('/api/post/'+this.id, formData)
      .then(response => {
        swal.close();
        console.log(response.data);
        this.success('Post updated successfully');
        this.post = response.data.post;
        this.categorys = response.data.categorys;
        this.can_edit = response.data.can_edit;
        this.can_publish = response.data.can_publish;
        this.auth_id = response.data.auth_id;
        this.category = [];
        for(let category in response.data.post.categories){
          this.category.push(response.data.post.categories[category].id);
    //console.log(category);
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
  mounted(){
    window.url="";
    loader();
    swalclose();
    axios.get('/api/post/'+this.id+'/edit')
    .then(response => {
      swal.close();
      console.log(response.data);
      if (response.data.fail) {
        swal(response.data.fail, {
          icon: "error",
        });
        window.location.href = "/al-admin/post";
        return;
      }
      this.post = response.data.post;
      this.categorys = response.data.categorys;
      this.can_edit = response.data.can_edit;
      this.can_publish = response.data.can_publish;
      this.auth_id = response.data.auth_id;
      for(let category in response.data.post.categories){
        this.category.push(response.data.post.categories[category].id);
    //console.log(category);
  }
  //this.category = response.data.post.categories;
  //console.log(response.data.auth_id);
})
    .catch((error)=>{
      swal.close();
      console.log(error.response.data);
      this.err(error);
      
    })
  },
  updated(){
  //CKEDITOR.config.readOnly = !vnode.context.can_edit;
  var target_preview = $('#holder');
  $('#lfm').filemanager('image');
  if (!this.post.image||this.post.image===0) {
    return;
  }
  target_preview.html('');
  target_preview.append(
    $('<img>').css('height', 'auto').css('width', '100%').attr('src', this.post.image )
    );
  this.post.image="";
   //target_preview.html('');
 },
}



</script>

<style>

</style>
