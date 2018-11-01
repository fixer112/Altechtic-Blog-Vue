<template>
  <div class="row pb-2">
    <div class="col-md-8">
      <div class="card card-primary">
        <form @submit.prevent>
          <div class="card-body">
            <div class="form-group">
              <label>Title</label>
              <input class="form-control" v-model="title" @keyup="sluged">
            </div>
            <div class="form-group">
              <label>Slug</label>
              <input class="form-control" v-model="slug" @change="slugg">
            </div>
            <div class="form-group">
              <textarea class="form-control" id="editor" v-ckeditor></textarea>
              count: {{body.length}}
              
              <button class="btn btn-primary float-right mt-2" @click="preview">Preview content</button>
              
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="col-md-4">

      <div class="card card-primary">
        <div class="mb-2 card-body">
          <button v-if="publish" class="btn btn-success mr-auto" v-on:click="create('1')"><i class="fa fa-save"></i> Publish</button>
            <button class="btn btn-primary ml-auto" v-on:click="create('0')"><i class="fa fa-paperclip"></i> Draft</button><!-- 
            <button class="btn btn-danger"><i class="fa fa-trash"></i> Trash</button> -->
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
                  <input type="checkbox" :value="category.id" @click="add_category(category.id)">
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
          publish:"",
          categorys:[],
          category:[],
          id:"",
        }
      },
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
        //console.log(options);

        var editor = CKEDITOR.replace('editor', options);
        editor.on('change', function(e){
          vnode.context.body = e.editor.getData();
        });
      }
    },
  },
  methods:{
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
    slugg(){
      var nslug = this.slug.replace(/\s+/g,'-');
      this.slug = nslug.toLowerCase();
    },
    sluged(){
      var ntitle = this.title.replace(/\s+/g,' ');
      ntitle = ntitle.replace(/-+/,'-');
      this.title = ntitle;
      var nslug = ntitle.replace(/\s+/g,'-');
      nslug = nslug.replace(/-+/,'-');
      this.slug = nslug.toLowerCase();
    },
    submit(){

    },
    create(status){
      let formData = new FormData();
      formData.set('title', this.title);
      formData.set('slug', this.slug);
      formData.set('body', this.body);
      formData.set('status', status);
      if (url!='') {
        formData.set('photo_url', url);
      }
      if (this.category.length>0) {
        formData.set('category', JSON.stringify(this.category));
      }

      loader();
      axios.post('/api/post',formData)
      .then(response => {
        swal.close();
        console.log(response.data);
        this.success('Post created successfully');
        this.id = response.data.id;
  //location.reload();
  //console.log(status);
  window.location.href = "/al-admin/post/"+this.id+'/edit';
})
      .catch((error)=>{
        swal.close();
  //console.log(error.response.data);
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
    window.url="";
    loader();
    swalclose();
    axios.get('/api/post/create')
    .then(response => {
      swal.close();
      console.log(response.data);
      if (response.data.fail) {
        swal(response.data.fail, {
          icon: "error",
        });
        window.location.href = "/al-admin/post/";
        return;
      }
      this.publish = response.data.publish;
      this.categorys = response.data.categorys;
    })
    .catch((error)=>{
      swal.close();
    });
  },
  updated(){
    
  },
  mounted(){
  }
}



</script>

<style>

</style>
