<template>
  
</template>

<script>
  export default {
    data () {
      return {

      }
    },
    methods:{
      /*danger:function(error){
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
      },*/


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
