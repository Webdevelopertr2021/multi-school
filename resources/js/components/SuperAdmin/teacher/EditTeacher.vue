<template>
  <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Edit teacher</h4>
                <a href="#" class="btn btn-sm btn-primary" @click="$router.go(-1)"><i class="fas fa-arrow-left"></i> Go back</a>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-12 text-center">
                      <img v-if="teacherPhotoUrl == null" class="user-thumb-100" src="/image/portrait-placeholder.png" alt="">
                      <img v-else class="user-thumb-100" :src="teacherPhotoUrl" alt="">
                    </div>
                </div>
                <form @submit.prevent="updateTeacher" class="row">
                    <div class="col-md-4 mb-4">
                        <label for="">Select School</label>
                        <multiselect :class="{'is-invalid': form.errors.has('schoolId')}" v-model="selectedSchool" :options="schools" 
                        @input="getClassList();getSuperVisors()" :preserve-search="true" placeholder="Select section" label="name" track-by="id"></multiselect>
                        <HasError  :form="form" field="schoolId"/>
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
                        <label for="">Salary</label>
                        <input type="number" name="salary" class="form-control" :class="{'is-invalid' : form.errors.has('salary')}" v-model="form.salary" placeholder="Salary...">
                        <HasError :form="form" field="salary" />
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="">Total Credit for leave without salary</label>
                        <input type="number" name="credit_salry" class="form-control" :class="{'is-invalid' : form.errors.has('credit_without_salary')}" v-model="form.credit_without_salary" placeholder="Credit...">
                        <HasError :form="form" field="credit_without_salary" />
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="">Total Credit for Time Leave</label>
                        <input type="number" name="credit_time" class="form-control" :class="{'is-invalid' : form.errors.has('credit_time')}" v-model="form.credit_time" placeholder="Credit...">
                        <HasError :form="form" field="credit_time" />
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="">Change Password</label>
                        <input type="text" class="form-control" :class="{'is-invalid' : form.errors.has('password')}" 
                        v-model="form.password" placeholder="Set password...">
                        <HasError :form="form" field="password" />
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="">Change profile picture <small>(optional)</small></label>
                        <input type="file" class="form-control-file" 
                        :class="{'is-invalid' : form.errors.has('pp')}" @change="fileChange" accept="image/*">
                        <HasError :form="form" field="pp" />
                    </div>


                    <div class="col-12 mb-4">
                        <Button class="btn btn-success" :form="form">Update Teacher</Button>
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
                teacherId: this.$route.params.teacherId,
                schoolId: null,
                name: '',
                phone: '',
                email: '',
                password: '',
                pp: null,
                selectedSuperVisors: [],
                superVisors: null,
                classId : null,
                sectionId: null,
                salary: "",
                credit_without_salary: 1,
                credit_time: 3,
            }),
            schools: [],
            supervisor: [],
            classes: [],
            selectedSchool: null,
            selectedClass: null,
            selectedSection: null,
            isLoadingSec: false,
            sections: [],
            teacherPhotoUrl: null,

        }
    },
    methods: {
        getTeacherInfo() {
            axios.get("/admin/api/get-edit-teacher-data",{
                params: {
                    teacherId : this.$route.params.teacherId
                }
            }).then(resp=>{
                return resp.data;
            }).then(data=>{
                if(data.status == "ok") {
                    this.selectedSchool = data.selectedSchool;

                    this.classes = data.classes;
                    this.selectedClass = data.selectedClass;

                    this.sections = data.sections;
                    this.selectedSection = data.selectedSection;

                    this.supervisor = data.supervisors;
                    this.form.selectedSuperVisors = data.selectedSupervisors;

                    this.form.name = data.teacher.name;
                    this.form.phone = data.teacher.phone;
                    this.form.email = data.teacher.email;
                    this.form.schoolId = data.teacher.school_id;
                    this.form.classId = data.teacher.class_id;
                    this.form.sectionId = data.teacher.section_id;

                    this.form.salary = data.teacher.salary;
                    this.form.credit_without_salary = data.teacher.credit_without_salary;
                    this.form.credit_time = data.teacher.credit_time;

                    if(data.teacher.photo_url != null)
                    {
                        this.teacherPhotoUrl = data.teacher.photo_url;
                    }
                }
                else {
                    this.$router.push({
                        name: 'admin.all-teacher'
                    });
                }
            }).catch(err=>{
                this.$router.push({
                    name: 'admin.all-teacher'
                });
                console.error(err.response.data);
            })
        },
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
              schoolId: this.selectedSchool.id,
            }
          }).then(resp=>{
            return resp.data;
          }).then(data=>{
            
            this.supervisor = data;
            
          }).catch(err=>{
            console.error(err.response.data);
          })
        },
        async updateTeacher() {

            if(this.selectedSchool != null)
            {
                this.form.schoolId = this.selectedSchool.id;
            }
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
            
            await this.form.post("/admin/api/update-teacher").then(resp=>{
                return resp.data;
            }).then(data=>{
              console.log(data);
                if(data.status == "ok") {
                    swal.fire("Teacher added",data.msg,"success").then(()=>{
                        this.$router.push({
                            name: 'admin.all-teacher',
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
            
            axios.get("/admin/api/get-class-list?schoolId="+this.selectedSchool.id).then(resp=>{
                return resp.data;
            }).then(data=>{
                this.classes = data;
            }).catch(err=>{
                console.error(err.response.data);
            });
        },
        getSectionList() {
            this.sections = [];
            this.selectedSection = null;
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
        this.getTeacherInfo();
        this.getSchools();
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