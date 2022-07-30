<template>
  <!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit school - {{ form.name }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="updateSchool" class="row">
            <div class="col-md-12 form-group">
                <label for="">School Name</label>
                <input type="text" class="form-control" placeholder="School name" v-model="form.name" :class="{'is-invalid':form.errors.has('name')}">
                <HasError :form="form" field="name" />
            </div>

            <div class="col-md-12 form-group">
                <label for="">School Address</label>
                <textarea type="text" class="form-control" placeholder="School address" 
                v-model="form.address" :class="{'is-invalid':form.errors.has('address')}"></textarea>
                <HasError :form="form" field="address" />
            </div>

            <div class="col-md-12 form-group">
                <label for="">School Code</label>
                <input type="text" class="form-control" placeholder="School code" v-model="form.schoolCode" :class="{'is-invalid':form.errors.has('schoolCode')}">
                <HasError :form="form" field="schoolCode" />
            </div>

            <div class="col-md-12 form-group">
                <Button class="btn btn-success" :form="form" type="submit">Update</Button>
            </div>
            
        </form>
      </div>
    </div>
  </div>
</div>
</template>

<script>

export default {
    props: ["schoolData","index"],
    data() {
        let data = this.schoolData;
        return {
            form: new Form({
                id: "",
                name: "",
                address: "",
                schoolCode: "",
            })
        }
    },
    methods: {
        updateSchool(){
          this.form.post("/admin/api/update-school").then(resp=>{
            return resp.data;
          }).then(data=>{
            console.log(data);
            if(data.status == "ok")
            {
              this.updated(data.newData,data.msg,this.index);
            }
          }).catch(err=>{
            console.error(err.response.data);
          })
        },
        updated(data,msg,index) {
          this.$emit("updated",data,msg,index);
        }
    },
    watch: {
      schoolData: function(newData) {
        this.form.id = newData.id;
        this.form.name = newData.name;
        this.form.address = newData.address;
        this.form.schoolCode = newData.code;
      }
    }
}
</script>

<style>

</style>