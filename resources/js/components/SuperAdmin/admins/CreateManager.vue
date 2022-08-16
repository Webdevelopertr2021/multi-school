<template>
  <div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Create new manager</h4>
                <router-link :to="{name: 'admin.manager-list'}" class="btn btn-sm btn-primary"><i class="fas fa-list"></i> Manager list</router-link>
            </div>
            <div class="card-body">
                <form @submit.prevent="addAdmin" class="row">
                  <div class="col-md-12 mb-4">
                    <label for="">Manager name</label>
                    <input type="text" class="form-control" v-model="form.name" placeholder="Manager name..." :class="{'is-invalid' : form.errors.has('name')}">
                    <HasError :form="form" field="name" />
                  </div>
                  <div class="col-md-12 mb-4">
                    <label for="">Phone</label>
                    <input type="tel" class="form-control" v-model="form.phone" placeholder="Manager phone..." :class="{'is-invalid' : form.errors.has('phone')}">
                    <HasError :form="form" field="phone" />
                  </div>

                  <div class="col-md-12 mb-4">
                    <label for="">Email</label>
                    <input type="email" class="form-control" v-model="form.email" placeholder="Manager email..." :class="{'is-invalid' : form.errors.has('email')}">
                    <HasError :form="form" field="email" />
                  </div>

                  <div class="col-md-12 mb-4">
                    <label for="">Password</label>
                    <input type="password" class="form-control" v-model="form.password" placeholder="Set password..." :class="{'is-invalid' : form.errors.has('password')}">
                    <HasError :form="form" field="password" />
                  </div>

                  <div class="col-md-12 mb-4">
                    <label for="">Photo</label>
                    <input type="file" class="form-control-file" accept="image/*" @change="fileChange" :class="{'is-invalid' : form.errors.has('photo')}">
                    <HasError :form="form" field="photo" />
                  </div>

                  <div class="col-md-12 mb-4">
                      <label for="access"><input type="checkbox" id="access" v-model="form.accessToLeave"> &nbsp; Can access teachers leave applications</label>
                  </div>

                  <div class="col-md-12 mb-4">
                    <Button :form="form" class="btn btn-success">Save</Button>
                  </div>

                </form>
            </div>
        </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: new Form({
        name: '',
        email : '',
        phone: '',
        password: 'school2022',
        photo: null,
        accessToLeave : false,
      })
    }
  },
  methods: {
    fileChange(e) {
      let file = e.target.files[0];
      if(file) {
        this.form.photo = file;
      }
      else {
        this.form.photo = null;
      }
    },
    async addAdmin() {
      await this.form.post('/admin/api/create-manager').then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {
          swal.fire("Added",data.msg,"success");
          this.form.reset();
        }
      }).catch(err=>{
        console.error(err.response.data);
      })
    }
  },
}
</script>

<style>

</style>