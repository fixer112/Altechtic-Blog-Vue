<template>
  <div class="col-md-8 mx-auto pb-2">
    <div class="card card-primary">
      <form @submit.prevent>
        <div class="card-body">
          <div class="form-group">
            <label>Site Name</label>
            <input class="form-control" :value="settings.sitename" type="text" @change="submit('sitename', $refs.sitename.value)" @keyup.enter="submit('sitename', $refs.sitename.value)" ref="sitename">
          </div>

          <div class="form-group">
            <label>Allow Comments</label>
            <select class="form-control"type="text" @change="submit('comments', $refs.comments.value)" ref="comments">
              <option :selected="settings.comments==1?'selected':''" value="1">Yes</option>
              <option :selected="settings.comments==0?'selected':''" value="0">No</option>
            </select>
          </div>

          <div class="form-group">
            <label>Approve Comment</label>
            <select class="form-control"type="text" @change="submit('approve_comment', $refs.approve_comment.value)" ref="approve_comment">
              <option :selected="settings.approve_comment==1?'selected':''" value="1">Yes</option>
              <option :selected="settings.approve_comment==0?'selected':''" value="0">No</option>
            </select>
          </div>

          <div class="form-group">
            <label>Posts Per Page</label>
            <input class="form-control" :value="settings.posts_per_page" type="number" @change="submit('posts_per_page', $refs.posts_per_page.value)" @keyup.enter="submit('posts_per_page', $refs.posts_per_page.value)" ref="posts_per_page">
          </div>

          <div class="form-group">
            <label>Comments Per Post</label>
            <input class="form-control" :value="settings.comments_per_posts" type="number" @change="submit('comments_per_posts', $refs.comments_per_posts.value)" @keyup.enter="submit('comments_per_posts', $refs.comments_per_posts.value)" ref="comments_per_posts">
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
        settings:"",
      }
    },
    methods:{
      danger:function(error){
        this.$notify.danger(error,{
          iconClass: 'fa fa-exclamation-triangle',
          visibility:5000,
          permanent:true,
        });
      },
      success:function(msg){
        this.$notify.success(msg,{
          iconClass: 'fa fa-check',
          visibility:5000,
        });
      },
      submit(key, value){
    //console.log(id);
    //console.log(value);
    loader();
    axios.put('/api/setting/change',{
      key:key,
      value:value,
    })
    .then(response => {
      swal.close();
      //console.log(response.data);
      this.settings = response.data;
      this.success("Change sucessfull");
    })
    .catch((error)=>{
      swal.close();
      console.log(error);
      this.danger("An Error occured");
      
    })
    
  },

},
created(){
  //console.log(this.$refs.input1['0']);
  loader();
  swalclose();
  axios.get('/api/setting')
  .then(response => {
    swal.close();
    console.log(response.data);
    this.settings = response.data;
  })
  .catch((error)=>{
    swal.close();
      //console.log(error.response.);
      
    })
}
}



</script>

<style>

</style>
