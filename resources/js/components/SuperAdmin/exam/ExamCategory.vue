<template>
  <div class="row justify-content-center">

    <div class="col-md-7 mb-3" v-if="addMode">
      <div class="card">
        <div class="card-header">
          <h4>Add category</h4>
        </div>
        <div class="card-body">
          <form @submit.prevent="addCat" class="row">
            <div class="col-md-12 mb-4">
              <label for="">Name</label>
              <input type="text" :class="{'is-invalid' : addForm.errors.has('name')}" class="form-control" placeholder="Category name..." v-model="addForm.name">
              <HasError :form="addForm" field="name" />
            </div>
            <div class="col-md-12 mb-4">
              <Button class="btn btn-success" :form="addForm" type="submit">Add</Button>
              <button @click="addMode=false;" class="btn btn-danger" type="button">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-7 mb-3" v-if="editMode">
      <div class="card">
        <div class="card-header">
          <h4>Edit category</h4>
        </div>
        <div class="card-body">
          <form @submit.prevent="updateCat" class="row">
            <div class="col-md-12 mb-4">
              <label for="">Name</label>
              <input type="text" :class="{'is-invalid' : editForm.errors.has('name')}" class="form-control" placeholder="Category name..." v-model="editForm.name">
              <HasError :form="editForm" field="name" />
            </div>
            <div class="col-md-12 mb-4">
              <Button class="btn btn-primary" :form="editForm" type="submit">Update</Button>
              <button @click="editMode=false;editForm.reset();" class="btn btn-danger" type="button">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>All Category List</h4>
                <button @click="addMode=true;editMode=false" class="btn btn-sm btn-primary">Add Category</button>
            </div>
            <div class="card-body">
                <div class="row" v-if="isLoading">
                  <div class="col-md-12 mb-3" v-for="n in 10" :key="n">
                    <skeleton width="100%" height="30px" />
                  </div>
                </div>
                <div class="row" v-else>
                  <div class="col-md-12">
                    <div class="table-responsive">
                      <table class="table table-hover table-bordered">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th class="text-right">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(cat,i) in cats" :key="i">
                            <td>{{ i+1 }}</td>
                            <td>{{ cat.name }}</td>
                            <td class="text-right">
                              <button @click="toggleEdit(cat,i)" class="btn btn-sm btn-warning">Edit</button>
                              <button @click="deleteCat(cat.id,i)" class="btn btn-sm btn-danger">Delete</button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isLoading: true,
      addForm: new Form({
        name: "",
      }),
      editForm: new Form({
        catId: "",
        name: "",
        index: "",
      }),
      cats: [],
      addMode: false,
      editMode: false,

    }
  },
  methods: {
    getExamCats() {
      axios.get("/admin/api/get-exam-categories").then(resp=>{
        return resp.data;
      }).then(data=>{
        this.cats = data;
        this.isLoading = false;
      }).catch(err=>{
        console.error(err.response.data);
      })
    },
    addCat() {
      this.addForm.post('/admin/api/save-exam-cat').then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {
          swal.fire("Done",data.msg,"success");
          this.cats.push(data.cat);
          this.addForm.reset();
        }
      }).catch(err=>{
        console.error(err.response.data);
      })
    },
    updateCat() {
      this.editForm.post('/admin/api/update-exam-cat').then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {
          swal.fire("Done",data.msg,"success");
          this.cats[this.editForm.index].name = data.cat.name;
          this.editMode = false;
          this.editForm.reset();
        }
      }).catch(err=>{
        console.error(err.response.data);
      })
    },
    toggleEdit(data,index) {
      this.editForm.catId = data.id;
      this.editForm.name = data.name;
      this.editForm.index = index;
      this.editMode = true;
      this.addMode = false;
    },
    deleteCat(id,index) {
      swal.fire({
          title: 'Are you sure?',
          text: "This will delete all the exams and the questions related with this category. You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
          if (result.isConfirmed) {
          axios.post("/admin/api/delete-exam-cat",{
              catId: id,
          }).then(resp=>{
              return resp.data;
          }).then(data=>{
              if(data.status == "ok")
              {
                swal.fire("Removed",data.msg,"success");
                this.cats.splice(index,1);
              }
          }).catch(err=>{
              toastr.error("Failed to delete","Internal Server error");
              console.error(err.response.data);
          })
          }
      }); //swal
    }
  },
  mounted() {
    this.getExamCats();
  }
}
</script>

<style>

</style>