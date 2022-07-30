<template>
  <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Add teacher</h4>
                <router-link class="btn btn-sm btn-primary" 
                :to="{name: 'admin.school-details', params: {schoolId: form.school}}"><i class="fas fa-arrow-left"></i> Go back to school</router-link>
            </div>
            <div class="card-body">
                <form @submit.prevent="addTeacher" class="row">
                    <div class="col-md-4 mb-4">
                        <label for="">Select School</label>
                        <select class="form-control" v-model="form.school" disabled>
                            <option value="" selected hidden>Select School</option>
                            <option v-for="(school,i) in schools" :key="i" :value="school.id">{{ school.name }}</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-4">
                      <label for="">Select Supervisors</label>
                      <multiselect :class="{'is-invalid': form.errors.has('superVisors')}" v-model="form.selectedSuperVisors" :options="supervisor"
                       :multiple="true" :preserve-search="true" placeholder="Select supervisors"
                       label="name" track-by="name" :closeOnSelect="false"></multiselect>
                       <HasError :form="form" field="superVisors" />
                    </div>
                    <div class="col-md-4 mb-4">
                        <label for="">Teacher name</label>
                        <input type="text" class="form-control" :class="{'is-invalid' : form.errors.has('name')}" v-model="form.name" placeholder="Teacher name">
                        <HasError :form="form" field="name" />
                    </div>
                    <div class="col-md-4 mb-4">
                        <label for="">Phone number</label>
                        <input type="tel" class="form-control" :class="{'is-invalid' : form.errors.has('phone')}" v-model="form.phone" placeholder="Phone number...">
                        <HasError :form="form" field="phone" />
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="">Email <small>(optional)</small></label>
                        <input type="email" name="email" class="form-control" :class="{'is-invalid' : form.errors.has('email')}" v-model="form.email" placeholder="Email...">
                        <HasError :form="form" field="email" />
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="">Password</label>
                        <input type="text" class="form-control" :class="{'is-invalid' : form.errors.has('password')}" 
                        v-model="form.password" placeholder="Set password...">
                        <HasError :form="form" field="password" />
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="">Set profile picture <small>(optional)</small></label>
                        <input type="file" class="form-control-file" 
                        :class="{'is-invalid' : form.errors.has('pp')}" @change="fileChange" accept="image/*">
                        <HasError :form="form" field="pp" />
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="">Select Class</label>
                        <multiselect @input="getSectionList" :class="{'is-invalid': form.errors.has('classId')}" v-model="selectedClass" :options="classes" 
                        :preserve-search="true" placeholder="Select class" label="name" track-by="id"></multiselect>
                        <HasError  :form="form" field="classId"/>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="">Select section</label>
                        <multiselect :class="{'is-invalid': form.errors.has('sectionId')}" v-model="selectedSection" :options="sections" 
                        :preserve-search="true" placeholder="Select section" label="name" track-by="id" :loading="isLoadingSec"></multiselect>
                        <HasError  :form="form" field="sectionId"/>
                    </div>


                    <div class="col-12 mb-4">
                        <Button class="btn btn-success" :form="form">Add Teacher</Button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
  </div>
</template>

<script>

import Multiselect from "vue-multiselect";

export default {
  components: {
    "multiselect" : Multiselect,
  },
  data() {
        return {
            form : new Form({
                school: this.$route.params.schoolId,
                name: '',
                phone: '',
                email: '',
                password: 'school2022',
                pp: null,
                selectedSuperVisors: [],
                superVisors: null,
                classId : null,
                sectionId: null,
            }),
            schools: [],
            supervisor: [],
            classes: [],
            selectedClass: null,
            selectedSection: null,
            isLoadingSec: false,
            sections: [],

        }
    },
    methods: {
        getSchools() {
            axios.get("/admin/api/get-all-schools").then(resp=>{
                return resp.data;
            }).then(data=>{
                this.schools = data;
            }).catch(err=>{
                console.error(err.response.data);
            });
        },
        getSuperVisors() {
          axios.get("/admin/api/get-supervisors",{
            params: {
              schoolId: this.$route.params.schoolId,
            }
          }).then(resp=>{
            return resp.data;
          }).then(data=>{
            
            this.supervisor = data;
            
          }).catch(err=>{
            console.error(err.response.data);
          })
        },
        async addTeacher() {

            if(this.selectedClass != null) 
            {
                this.form.classId = this.selectedClass.id;
            }
            if(this.selectedSection != null)
            {
                this.form.sectionId = this.selectedSection.id;
            }
            if(this.form.selectedSuperVisors.length > 0)
            {
                this.form.superVisors = JSON.stringify(this.form.selectedSuperVisors);
            }
            
            await this.form.post("/admin/api/add-teacher").then(resp=>{
                return resp.data;
            }).then(data=>{
              console.log(data);
                if(data.status == "ok") {
                    swal.fire("Teacher added",data.msg,"success").then(()=>{
                        this.$router.push({
                            name: 'admin.school-details',
                            params: {
                                schoolId: this.$route.params.schoolId
                            }
                        })
                    })
                }
            }).catch(err=>{
                toastr.error("Failed","Internal server error");
                console.error(err.response.data);
            })
        },
        fileChange(e) {
            let file = e.target.files[0];
            if(file) {
                this.form.pp = file;
            }
            else {
                this.form.pp = null;
            }
        },
        getClassList() {
            axios.get("/admin/api/get-class-list?schoolId="+this.$route.params.schoolId).then(resp=>{
                return resp.data;
            }).then(data=>{
                this.classes = data;
            }).catch(err=>{
                console.error(err.response.data);
            });
        },
        getSectionList() {
            this.sections = [];
            this.isLoadingSec = true;
            axios.get("/admin/api/get-section-list?classId="+this.selectedClass.id).then(resp=>{
                return resp.data;
            }).then(data=>{
                console.log(data);
                this.sections = data;
                this.isLoadingSec = false;
            }).catch(err=>{
                console.error(err.response.data);
            })
        },
    },
    mounted() {
        this.getSchools();
        this.getClassList();
        this.getSuperVisors();
    }
}
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style>
.multiselect__option--highlight {
    background: #6170df !important;
}
.multiselect__option--highlight::after{
  background: #6170df !important;
}
.multiselect__tag{
  background: #6170df !important;
}
.multiselect__option--selected{
  background-color: #e58787 !important;
}
.multiselect__option--selected::after{
  background-color: #e58787 !important;
}
.is-invalid{
  border: 1px solid red;
}
</style>